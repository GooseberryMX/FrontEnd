<?php

namespace AppBundle\Controller;

use AppBundle\Util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\CompleteRegistrationInvestorType;
use AppBundle\Form\UserType;
use AppBundle\Form\UserCustomInfoType;
use AppBundle\Form\OrganizationType;
use AppBundle\Form\OfferingType;
use AppBundle\Form\SignInType;
use AppBundle\Form\EquityType;
use AppBundle\Form\LoanType;
use AppBundle\Form\InvestorsType;
use AppBundle\Form\FundraisersType;

class ProfileController extends BaseController
{
    /**
     * @Route("/your-profile", name="your_profile")
     *
     */
    public function yourProfileAction(Request $request)
    {
        //Add breadcrumb
        $bc = 'Profile';
        $profile = $this->get('session')->get('userInfo');
        
        //if (!$profile['email_verified']) {
        //    $this->get('session')->getFlashBag()->add('cv_msg', 'Please verify your email address.');
        //
        //    return $this->redirect($this->generateUrl('homepage'));
        //}
        
        //if (!$profile['registration_complete'])
        //    return $this->redirect($this->generateUrl('complete_registration'));
        
        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $key => $value) {
                $changePassData[$key] = $value;
            }
            $data['pass'] = array();
            $data['pass']['current_password'] = $changePassData['current_password'];
            $data['pass']['new_password'] = $changePassData['new_password'];
            $data['pass']['new_password_again'] = $changePassData['new_password_again'];
            $data['personid'] = $this->user['profile_id'];
            $data['action'] = "change-password";

            $process = $this->api->createProcess('process:person_update', $data);
            if (!$process->isOk())
                $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
            else
                $this->get('session')->getFlashBag()->add('cv_msg', 'Change password successfully.');
        }

        return $this->render('AppBundle:Profile:your-profile.html.twig',
            array('profile' => $profile,
                'bc' => $bc
            )
        );
    }   
   
    /**
     * @Route("/edit-profile", name="edit_profile")
     */
    public function editProfileAction(Request $request)
    {
        $bc = 'Profile';
        $profile = $this->get('session')->get('userInfo');

        $request = $this->get('request');
        //if (!$profile['email_verified']) {
        //    $this->get('session')->getFlashBag()->add('cv_msg', 'Please verify your email address.');
		//
        //    return $this->redirect($this->generateUrl('homepage'));
        //}
        
        if (!$profile['registration_complete'])
            return $this->redirect($this->generateUrl('complete_registration'));
            
        $formInvestor = $this->createForm(new InvestorsType());
        $formFondraisers = $this->createForm(new FundraisersType());
        if ($this->get('session')->get('isInvestor')) {
            $formInvestor->get('investor_type')->setData("INVESTOR");
            $formInvestor->get('date_of_birth')->setData($this->profile['date_of_birth']);
            $formInvestor->get('tax_id_number')->setData($this->profile['tax_id_number']);
            //$formInvestor->get('nationality')->setData($this->profile['nationality']);
            $formInvestor->get('address')->setData($this->profile['address']);
            $formInvestor->get('neighborhood')->setData($this->profile['neighborhood']);
            $formInvestor->get('city')->setData($this->profile['city']);
            $formInvestor->get('state')->setData($this->profile['state']);
            $formInvestor->get('country')->setData($this->profile['country']);
            $formInvestor->get('zip')->setData($this->profile['zip']);
            $formInvestor->get('phone')->setData($this->profile['phone']);
            //$formInvestor->get('bank_account_id')->setData($this->profile['bank_account_id']);
            $formInvestor->get('file_scan_id')->setData(isset($this->profile['file_scan_id']) ? $this->formatS3Json($this->profile['file_scan_id']) : '{"name":"No file","s3_url":"No link"}');

        } else {
            $formFondraisers->get('investor_type')->setData("FUNDRAISER");
            $formFondraisers->get('company_name')->setData($this->profile['company_name']);
            $formFondraisers->get('tax_id_number')->setData($this->profile['tax_id_number']);
            $formFondraisers->get('company_address')->setData($this->profile['company_address']);
            $formFondraisers->get('neighborhood')->setData($this->profile['neighborhood']);
            $formFondraisers->get('office_address_city')->setData($this->profile['office_address_city']);
            $formFondraisers->get('state')->setData($this->profile['state']);
            $formFondraisers->get('office_address_country')->setData($this->profile['office_address_country']);
            $formFondraisers->get('zip')->setData($this->profile['zip']);
            $formFondraisers->get('company_phone')->setData($this->profile['company_phone']);
        }

        if ($request->isMethod('POST')) {
            $formInvestor->bind($request);
            $formFondraisers->bind($request);
            $signUpInvestor = $formInvestor->getData();
            $signUpFundraiser = $formFondraisers->getData();
            if (isset($signUpInvestor['investor_type'])) {
                if ($formInvestor->isValid()) {
                    $process = $this->api->createProcess('process:register_complete', $signUpInvestor);
                    if (!$process->isOk())
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                    else
                        return $this->redirect($this->generateUrl('cv_gooseberry_your_profile', array('network' => $network)));
                }
            } else {
                if ($formFondraisers->isValid()) {
                    $process = $this->api->createProcess('process:register_complete', $signUpFundraiser);
                    if (!($process->isOk())) {
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                    } else {
                        return $this->redirect($this->generateUrl('cv_gooseberry_your_profile', array('network' => $network)));
                    }
                }
            }
        }
        return $this->render('CVGooseberryBundle:Profile:edit_profile.html.twig',
            array('network' => $this->network,
                'form_investors' => $formInvestor->createView(),
                'form_fondraisers' => $formFondraisers->createView(),
                'profile' => $this->profile,
                'bc' => $this->bc,
            ));
    }
    /**
     * @Route("/complete-registration", name="complete_registration")
     */
    public function completeRegistrationAction(Request $request)
    {
        $bc = 'Complete Registration';
        $profile = $this->get('session')->get('userInfo');

        if (($profile['registration_complete']))
            return $this->redirect($this->generateUrl('home'));
        $request = $this->get('request');
        $formInvestor = $this->createForm(new InvestorsType());
        $formFondraisers = $this->createForm(new FundraisersType());

        if ($request->isMethod('POST')) {
            $formInvestor->bind($request);
            $formFondraisers->bind($request);
            $signUpInvestor = $formInvestor->getData();
            $signUpFundraiser = $formFondraisers->getData();
            if (isset($signUpInvestor['investor_type'])) {
                if ($formInvestor->isValid()) {
                    $signUpInvestor['role'] = 'inv';
                    $process = $this->api->createProcess('process:register_complete', $signUpInvestor);
                    if (!($process->isOk())) {
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                    } else {
                        return $this->redirect($this->generateUrl('cv_gooseberry_home', array('network' => $network)));
                    }
                }
            } else {
                if ($formFondraisers->isValid()) {
                    $signUpFundraiser['role'] = 'fun';
                    $process = $this->api->createProcess('process:register_complete', $signUpFundraiser);
                    if (!($process->isOk())) {
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                    } else {
                        return $this->redirect($this->generateUrl('cv_gooseberry_home', array('network' => $network)));
                    }
                }
            }
        }
        return $this->render('AppBundle:Profile:completeRegistration.html.twig',
            array('form_investors' => $formInvestor->createView(),
                'form_fondraisers' => $formFondraisers->createView(),
                'profile' => $profile,
                'bc' => $bc,
            ));
    }   
   
    /**
     * @Route("/watchlist", name="watch_list")
     *
     */
    public function watchlistAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $limit = 12 * $page;

        $watchlist = $this->get('cv.api.plan')->getList(array('watchlist_profile_id' => $this->user['profile_id'],)
            , $limit, $page, array('email' => 'asc'));

        return $this->render('CVGooseberryBundle:Company:watch_list.html.twig',
            array('network' => $this->network,
                'watchlist' => $watchlist['data'],
            ));   
    }
        
    /**
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(new SignInType());
        if($request->isMethod('POST')){
            $form->handleRequest($request);
//            if($form->isValid()){
                $signInData = $form->getData();
                $authentication = $this->get('public')->authenticate($this->container->getParameter('cv_network'), $signInData['email'], $signInData['password']);

                if(!$authentication == false){
                    //FIXME
                    // set authenticated
                    $this->get('session')->set('authenticated', true);
                    $userInfo = $this->get('user')->getUserInfo();
                    $this->get('session')->set('userInfo', $userInfo);
                    if(isset($userInfo['is_admin']) && $userInfo['is_admin'] == true){
                        $this->get('session')->set('is_admin', true);
                    }
                    return $this->redirect($this->generateUrl('homepage'));
                }else{
                    $this->get('session')->getFlashBag()->add('errors','Username or Password is not correct, please try again.');
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }

        return $this->render('AppBundle:Profile:change_password.html.twig');
    }    
    
    /**
     * Update avatar
     *
     * @Route("/update-avatar", name="update_avatar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAvatarAction(Request $request)
    {
        $response = $this->get('user')->update($request->request->all());
        if (!empty($response['outcome']) && $response['outcome'] == 'success') {
            $this->get('session')->getFlashBag()->add('info', "Your avatar was successfully updated.");
        }else{
            $this->get('session')->getFlashBag()->add('error', $response['data']['user_message']);
        }
        return $this->redirect($this->generateUrl('my_profile'));
    }

}
