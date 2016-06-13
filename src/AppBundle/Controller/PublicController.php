<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SignInType;
use AppBundle\Form\SignUpType;
use AppBundle\Util\Util;
use Symfony\Component\HttpFoundation\Response;
use CrowdValley\Bundle\ClientBundle\Entity\CVResponse;
use CrowdValley\Bundle\ClientBundle\Entity\Offering;
use CrowdValley\Bundle\ClientBundle\Entity\Organization;

class PublicController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
		$featuredOfferingsResponse = $this->get('public')->getFeaturedOfferings();
		$organizationsResponse = $this->get('public')->getOrganizations();
		
		foreach ($featuredOfferingsResponse->objectList as $offering) {
			foreach ($organizationsResponse->objectList as $organization) {
				if ($organization->id == $offering->organization) {
					$offering->organization = $organization;
				}
			}
		}
				
        return $this->render('AppBundle:Public:index.html.twig', array(
        	'offerings' => $featuredOfferingsResponse->objectList
        ));
    }

    /**
     * @Route("/_change_language", name="cv_gooseberry_change_language")
     */
    public function changeLanguageAction(Request $request)
    {

        return $this->render('AppBundle:Public:index.html.twig');
    }
    
    /**
     * @Route("/contact-us", name="contact_us")
     */
    public function contactUsAction()
    {
        $this->params['menu_item']= 'contact_us';
        return $this->render('AppBundle:Public:contact_us.html.twig',$this->params);
    }

    /**
     * @Route("/legal", name="legal")
     */
    public function viewLegalAction()
    {
        $this->params['menu_item']= 'legal';
        return $this->render('AppBundle:Public:legal.html.twig',$this->params);
    }
    
    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction()
    {
        $this->params['menu_item']= 'forgot_password';
        return $this->render('AppBundle:Public:forgot_password.html.twig',$this->params);
    }
    
    /**
     * @Route("/sign-in", name="sign_in")
     */
    public function signInAction(Request $request)
    {
        $form = $this->createForm(new SignInType());
        if($request->isMethod('POST')){
            $form->handleRequest($request);

                $signInData = $form->getData();

                $authentication = $this->get('authenticate')->login($signInData['email'], $signInData['password']);

                if(!$authentication == false){
                
                    $this->get('session')->set('authenticated', true);
                    $userInfo = $this->get('user')->getUserInfo();
                    $this->get('session')->set('userInfo', $userInfo);

                    return $this->redirect($this->generateUrl('homepage'));
                }else{
                    $this->get('session')->getFlashBag()->add('errors','Username or Password is not correct, please try again.');
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }

		$this->params['form'] = $form;
        return $this->render('AppBundle:Public:sign_in.html.twig', $this->params);
    }


    /**
     * @Route("/sign-up", name="sign_up")
     */
    public function signUpAction(Request $request)
    {
        $form = $this->createForm(new SignUpType());
        
        if($request->isMethod('POST')){
            $form->submit($request);
            $signUpData = $request->request->get('sign_up_type');

            $url = $this->generateUrl('verify_email',array(), true);
            $additionalData = array(
            	'given_name' => $signUpData['given_name'],
            	'family_name' => $signUpData['family_name']
            );
            
            $registerResponse = $this->get('user')->createUser($signUpData['email'], $signUpData['password'], $url, $additionalData);

			if ($registerResponse->outcome == CVResponse::RESPONSE_OUTCOME_SUCCESS) {
                $this->addFlash('info','Thanks for registering to the platform. Please click the link in your email in order to continue the registration process.');
                return $this->redirectToRoute('homepage');			
			}
			            
            else {
                if (isset($registerResponse->exception->exceptionReference)) {
                
                	if ($registerResponse->exception->exceptionReference == 20009) {
						$forgotPasswordURL = $this->generateUrl('forgot_password');
						$msg = "Email account is already in use. If you have forgotten your password, click <a href='".$forgotPasswordURL."'>here</a>";
						$this->addFlash('errors',$msg);
                	}
                }else{
                    $this->addFlash('errors',$registerResponse->exception->userMessage);
                    return $this->redirectToRoute('homepage');                    
                }

            }
            return $this->redirectToRoute('homepage');
        }
        return $this->render('AppBundle:Public:sign_up.html.twig',array('form'=>$form->createView()));
    }

    /**
     * Logout
     *
     * @Route("/logout", name="logout")
     */
    public function signOutAction(Request $request)
    {
        $required = $request->query->get('required', '');
        if($this->get('session')->has('authenticated') && $this->get('session')->get('authenticated'))
        {
            $this->get('session')->set('authenticated', false);
            $this->get('security.token_storage')->setToken(null);
            $this->get('request')->getSession()->invalidate();
        }
        
        if ($required === 'login') {
            return $this->redirectToRoute('homepage', array('required' => 'login'));
        } else {
            return $this->redirectToRoute('homepage');
        }        
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/verify-email", name="verify_email")
     */
    public function verifyEmailAction(Request $request)
    {
        $network = $this->container->getParameter('cv_network');
        $user_id = $request->query->get('user_id');
        $secret  = $request->query->get('secret');
        $params = array(
            'user_id' => $user_id,
            'secret' => $secret
        );
                
        $this->get('public')->verifyEmail('<network>', $params);

        return $this->redirect($this->generateUrl('accreditation'));
    }

    /**
     * @Route("/put-s3-url", name="put_s3aws")
     *
     * @return Response
     */
    public function s3AwsAction()
    {
        $request = $this->get('request');
        $fileName = $request->get('name');
        $fileType = $request->get('type');
        $fileObject = Util::alphaNumCodeGenerator();
        $parameters = array('file_name' => $fileName, 'file_type' => $fileType, 'file_object' => $fileObject);
        $ret = $this->get('public')->getS3URL($this->container->getParameter('cv_network'), $parameters);

        return new Response($ret['url']['url'], 200);
    }
}
