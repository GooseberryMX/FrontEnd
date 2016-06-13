<?php

Namespace CrowdValley\GooseberryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EquityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('network-id', 'hidden')
            ->add('funding_round_id', 'hidden')
            ->add('status', 'hidden')
            ->add('ui_type', 'hidden')
            ->add('name', 'text',array(
                'required'=> false,
                'constraints' => array(
                    new NotBlank(),
                ),
            ))
            //->add('description', 'text',array('required'=> false,'max_length' => 200))
            ->add('detail', 'textarea',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            //->add('investment_required', 'text',array('required'=> false))

            ->add('logo', 'hidden')
            ->add('team', 'hidden')

            // EQUITY FIELDS
            ->add('target_amount', 'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))


            ->add('opening_date' ,'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank()
//                ),
            ))
            ->add('closing_date' , 'text',array(
                'required'=> false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('income_statement', 'hidden')
            ->add('balance_sheet', 'hidden')
            ->add('business_plan', 'hidden')
            ->add('use_of_funds', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('risks_and_mitigants', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('industry_name', 'text', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('qna', 'textarea', array(
                'required' => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
			->add('fina_financial_statement', 'hidden')
            ->getForm();
    }

    public function getName()
    {
        return 'opportunity_form';
    }
}
