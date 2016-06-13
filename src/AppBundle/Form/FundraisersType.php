<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FundraisersType extends AbstractType
{

    private  function getYesNoOptions(){
        $arr = array(
            'Yes' =>'Yes',
            'No' =>'No'
        );
        return $arr;
    }

    private  function getStates(){
        $arr = array(
            'Alberta' =>'Alberta',
            'British Columbia' =>'British Columbia',
            'Manitoba' =>'Manitoba',
            'New Brunswick' =>'New Brunswick',
            'Newfoundland and Labrador' =>'Newfoundland and Labrador',
            'Nova Scotia' =>'Nova Scotia',
            'Ontario' =>'Ontario',
            'Prince Edward Island' =>'Prince Edward Island',
            'Quebec' =>'Quebec',
            'Saskatchewan' =>'Saskatchewan',
        );
        return $arr;
    }

    private function getAllCountries(){
        \Locale::setDefault('en');
        $countries = Intl::getRegionBundle()->getCountryNames();
        $arr = array();
        if(is_array($countries)){
            $arr = array_combine($countries, $countries);
        }else{
            $arr =  array('CA'=>'Canada');//return common countries
        }
        return $arr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gvc_role', 'hidden')
            ->add('role', 'hidden')
            ->add('screenName', 'hidden')
            ->add('investor_type', 'hidden')
            ->add('company_name', 'text',array('required'=> true))
            ->add('tax_id_number', 'text',array('required'=> true))
            ->add('company_address', 'text',array('required'=> true))
            ->add('neighborhood', 'text',array('required'=> true))
            ->add('office_address_city', 'text',array('required'=> true))
            ->add('state', 'text',array('required'=> true))
            ->add('office_address_country', 'choice', array('required' => true,
                'choices' => $this->getAllCountries(),'preferred_choices' => array('Canada')
            ))
            ->add('zip', 'text',array('required'=> true))
            ->add('company_phone', 'text',array('required'=> true))
            ->getForm();
    }
    public function getName()
    {
        return 'fundraiser_form';
    }

}
