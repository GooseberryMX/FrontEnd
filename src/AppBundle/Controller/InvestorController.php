<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProjectType;
use AppBundle\Form\InvestType;
use AppBundle\Util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CrowdValley\Bundle\ClientBundle\Entity\CVResponse;

class InvestorController extends BaseController
{

    /**
     * @Route("/equity", name="equity")
     */
    public function equityAction(Request $request)
    {
		$offerings = [];
		$offeringsResponse = $this->get('offering')->getOfferings();
		dump($offeringsResponse);
		
		if ($offeringsResponse->outcome == CVResponse::RESPONSE_OUTCOME_SUCCESS) {

			$organizationsResponse = $this->get('organization')->getOrganizations();
			
			foreach ($offeringsResponse->objectList as $offering) {
				
				if ($offering->category == 'Equity') {
					foreach ($organizationsResponse->objectList as $organization) {
						if ($organization->id == $offering->organization) {
							$offering->organization = $organization;
							$offerings[] = $offering;
						}
					}
				}
			}
		}		
		
		dump($offerings);

        return $this->render('AppBundle:Investor:equity.html.twig',
          array('offerings' => $offerings)
        );    
    }

    /**
     * @Route("/equity-overview/{offering_id}", name="equity_overview")
     */
    public function equityOverviewAction(Request $request, $offering_id)
    {
        if ($this->get('session')->get('authenticated') == false) {
        
            $this->get('session')->getFlashBag()->add('errors','Please Log In.');
            return $this->redirect($this->generateUrl('homepage'));        	
        }
        
		$offering = [];
		$offering['organization'] = [];
		$bc = '';
		
        $offeringResponse = $this->get('offering')->getOfferingWithId($offering_id);
		$organizationsResponse = $this->get('organization')->getOrganizations();
		    
		if ($offeringResponse->outcome == CVResponse::RESPONSE_OUTCOME_SUCCESS) {
		
			$offering = $offeringResponse->object;
			
			foreach ($organizationsResponse->objectList as $organization) {
				
				if ($organization->id == $offering->organization) {
					$offering->organization = $organization;
					$bc = $organization->displayName;
			}
		}
			
		}
        else {
        
            $this->get('session')->getFlashBag()->add('errors','Funding Round not found.');
            return $this->redirect($this->generateUrl('homepage'));        	        
        }

        return $this->render('AppBundle:Investor:equity_overview.html.twig',
          	array('offering' => $offering, 'bc' => $bc)
        );    
    }

    /**
     * @Route("/loans", name="loans")
     */
    public function loansAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $limit = 12 * $page;

        return $this->render('AppBundle:Investor:loans.html.twig'

        );    
    }

    /**
     * @Route("/make-investment/{offering_id}", name="make-investment")
     *
     * @param Request $request
     * @param $offering_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function investAction(Request $request, $offering_id)
    {
        // Check user is logged in
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(new InvestType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $data['investment_amount'] = 0;
            $data['life_cycle_stage'] = 0;
            $ret = $this->get('investment')->add($offering_id, $data);
            if (!empty($ret['outcome']) && $ret['outcome'] == 'success') {
                $this->addFlash('info', 'Your request has been successfully submitted.');
            } else {
                $this->addFlash('error', $ret['data']['user_message']);
            }
            $result = [
                'status' => 0,
                'url' => $this->generateUrl('view_company', ['offering_id' => $offering_id])
            ];
            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }
        return $this->redirectToRoute('equity_overview', array('offering_id' => $offering_id));
    }
      
}
