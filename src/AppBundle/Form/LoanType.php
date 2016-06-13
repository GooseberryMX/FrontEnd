<?php

Namespace CrowdValley\GooseberryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id', 'hidden')
            ->add('network-id', 'hidden')
            ->add('funding_round_id', 'hidden')
            ->add('status', 'hidden')
            ->add('ui_type', 'hidden')
            ->add('name', 'text',
                array(
                    'required'=> false,
//                    'constraints' => array(
//                        new NotBlank(),
//                    ),
                )
            )
            ->add('detail', 'textarea',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('interest_rate' , 'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('interest_payment_period' , 'choice',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
                'choices' => array('Annual' => 'Annual', 'Quarterly'=> 'Quarterly', 'Monthly'=> 'Monthly','Other'=>'Other'),
                'empty_value' => 'Choose...',
                'empty_data'  => null,
            ))
            ->add('type', 'hidden')
            ->add('qna', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            // LOAN FIELDS
            ->add('target_amount', 'text',array('required'=> false))
            ->add('opening_date' , 'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('closing_date' , 'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('collateral', 'text', array(
                    'required' => false,
//                    'constraints' => array(
//                        new NotBlank(),
//                    )
                )
            )
            ->add('industry_name', 'text', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('term', 'text', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('interest_only_period' , 'choice',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
                'choices' => $this->getInterestOnlyPeriodChoices(),
                'empty_value' => 'Choose...',
                'empty_data'  => null,
            ))
            ->add('convertible_non_convertible' , 'choice',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
                'choices' => array('Convertible' => 'Convertible', 'Non-convertible'=> 'Non-convertible'),
                'empty_value' => 'Choose...',
                'empty_data'  => null,
            ))
            ->add('logo', 'hidden')
            ->add('income_statement', 'hidden')
            ->add('balance_sheet', 'hidden')
            ->add('business_plan', 'hidden')
            ->add('amortization_schedule', 'hidden')
            ->add('risks_and_mitigants', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('use_of_funds', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('payment_sources', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('team', 'hidden')
            ->add('fina_financial_statement', 'hidden')
            ->getForm();
    }

    private function getInterestOnlyPeriodChoices(){
        $choices = array();
        for($i = 1 ; $i<= 28 ; $i++ ){
            $choices[$i] = $i;
        }
        $choices['N/A'] = 'N/A';
        return $choices;
    }

    public function getName()
    {
        return 'opportunity_form';
    }
}
