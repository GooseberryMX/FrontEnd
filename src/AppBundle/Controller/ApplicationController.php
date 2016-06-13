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

class ApplicationController extends BaseController
{   
    /**
     * @Route("/your-projects", name="your_projects")
     *
     */
    public function yourProjectsAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $limit = 12 * $page;
        //$companiesList = $this->get('cv.api.plan')
        //    ->getList(array('equity_only' => true, 'author' => $this->profile['id'][0]), $limit, $page, array('created' => 'desc'));

        return $this->render('CVGooseberryBundle:Company:your_projects.html.twig'
         //   array('network' => $this->network,
         //       'companies' => $companiesList['data'],
            );    
    
    }
    
    /**
     * @Route("/apply-loan", name="apply_loan")
     *
     */
    public function applyLoanAction(Request $request)
    {
        $form = $this->createForm(new LoanType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $opportunityData = $form->getData();
                $networkId = $this->network['id'][0];
                $opportunityData['network_id'] = $networkId;

                $opportunityData['visibility'] = 1;
                $opportunityData['advisor'] = $this->user['profile_id'];

                /* re-process qna */
                if (!empty($opportunityData['qna'])) {
                    $qnaSection = array();
                    $qnaSection[0]['section'] = 1;
                    $qnaSection[0]['question'] = $opportunityData['qna'];
                    $qnaSection[0]['answer'] = '';
                    $opportunityData['qna'] = json_encode($qnaSection);
                }

                if ($opportunityData['opening_date'] != '')
                    $opportunityData['opening_date'] = GooseberryHelper::convertStringToDatetime($opportunityData['opening_date']);
                if ($opportunityData['closing_date'] != '')
                    $opportunityData['closing_date'] = GooseberryHelper::convertStringToDatetime($opportunityData['closing_date']);
                if ($opportunityData['amortization_schedule'] != '')
                    $opportunityData['amortization_schedule'] = GooseberryHelper::formatS3Json($opportunityData['amortization_schedule']);
                if ($opportunityData['income_statement'] != '')
                    $opportunityData['income_statement'] = GooseberryHelper::formatS3Json($opportunityData['income_statement']);
                if ($opportunityData['balance_sheet'] != '')
                    $opportunityData['balance_sheet'] = GooseberryHelper::formatS3Json($opportunityData['balance_sheet']);
                if ($opportunityData['business_plan'] != '')
                    $opportunityData['business_plan'] = GooseberryHelper::formatS3Json($opportunityData['business_plan']);
                $opportunityData['fina_financial_statement'] = GooseberryHelper::formatS3Json($opportunityData['fina_financial_statement']);

                $process = $this->api->createProcess('process:plan_create', $opportunityData);

                if (!($process->isOk())) {
                    $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                } else {
                    $planId = $process['data']['id']->raw();
                    $fundingRoundData = array('business_plan_id' => $planId[0],
                        'author_id' => $this->user['profile_id'],
                        'type' => 'loan',
                    );

                    $fundingRoundData = array_merge($opportunityData, $fundingRoundData);

                    $process = $this->api->createProcess('process:funding_create', $fundingRoundData);
                    if(!$process->isOk())
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                    else
                        $this->get('session')->getFlashBag()->add('cv_msg', 'Please, waiting for approval.');

                    return $this->redirect($this->generateUrl('cv_gooseberry_loans',
                        array('network' => $network)));
                }
            }
        }

        return $this->render('CVGooseberryBundle:Company:apply_loan.html.twig',
            array('network' => $this->network,
                'form' => $form->createView(),
                'uniqueID' => UtilHelper::alphaNumCodeGenerator(),
            ));  
    
    }    
    
    
    /**
     * @Route("/your-loans", name="your_loans")
     *
     */
    public function yourLoansAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $limit = 12 * $page;
        //$companiesList = $this->get('cv.api.plan')
        //    ->getList(array('loans_only' => true, 'author' => $this->profile['id'][0]), $limit, $page, array('created' => 'desc'));

        return $this->render('CVGooseberryBundle:Company:your_loans.html.twig');    
    }

    /**
     * @Route("/apply-equity", name="apply_equity")
     *
     */
    public function applyEquityAction(Request $request)
    {
        $form = $this->createForm(new EquityType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $opportunityData = $form->getData();
                $networkId = $this->network['id'][0];
                $opportunityData['network_id'] = $networkId;

                /* re-process qna */
                if (!empty($opportunityData['qna'])) {
                    $qnaSection = array();
                    $qnaSection[0]['section'] = 1;
                    $qnaSection[0]['question'] = $opportunityData['qna'];
                    $qnaSection[0]['answer'] = '';
                    $opportunityData['qna'] = json_encode($qnaSection);
                }
                $opportunityData['visibility'] = 1;
                $opportunityData['advisor'] = $this->user['profile_id'];

                if ($opportunityData['opening_date'] != '')
                    $opportunityData['opening_date'] = GooseberryHelper::convertStringToDatetime($opportunityData['opening_date']);
                if ($opportunityData['closing_date'] != '')
                    $opportunityData['closing_date'] = GooseberryHelper::convertStringToDatetime($opportunityData['closing_date']);
                if ($opportunityData['logo'] != '')
                    $opportunityData['logo'] = GooseberryHelper::formatS3Json($opportunityData['logo']);
                if ($opportunityData['income_statement'] != '')
                    $opportunityData['income_statement'] = GooseberryHelper::formatS3Json($opportunityData['income_statement']);
                if ($opportunityData['balance_sheet'] != '')
                    $opportunityData['balance_sheet'] = GooseberryHelper::formatS3Json($opportunityData['balance_sheet']);
                if ($opportunityData['business_plan'] != '')
                    $opportunityData['business_plan'] = GooseberryHelper::formatS3Json($opportunityData['business_plan']);
                if ($opportunityData['fina_financial_statement'] != '')
				    $opportunityData['fina_financial_statement'] = GooseberryHelper::formatS3Json($opportunityData['fina_financial_statement']);

                $process = $this->api->createProcess('process:plan_create', $opportunityData);
                if (!($process->isOk())) {
                    $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                } else {
                    $planId = $process['data']['id']->raw();
                    $fundingRoundData = array('business_plan_id' => $planId[0],
                        'author_id' => $this->user['profile_id'],
                        'type' => 'equity'
                    );

                    $fundingRoundData = array_merge($opportunityData, $fundingRoundData);
                    $process = $this->api->createProcess('process:funding_create', $fundingRoundData);
                    if(!$process->isOk())
                        $this->get('session')->getFlashBag()->add('cv_error', $process->getError());
                     else
                        $this->get('session')->getFlashBag()->add('cv_msg', 'Please, waiting for approval.');

                    return $this->redirect($this->generateUrl('cv_gooseberry_equity',
                        array('network' => $network)));
                }
            }
        }

        return $this->render('CVGooseberryBundle:Company:apply_equity.html.twig',
            array('network' => $this->network,
                'form' => $form->createView(),
                'uniqueID' => UtilHelper::alphaNumCodeGenerator(),
            )
        );
    }
}
