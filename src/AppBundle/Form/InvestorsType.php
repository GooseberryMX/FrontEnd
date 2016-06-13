<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvestorsType extends AbstractType
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
            ->add('date_of_birth', 'text',array('required'=> false))
            ->add('tax_id_number', 'text',array('required'=> false))
            /*->add('nationality', 'choice', array('required' => true,
                    'choices' => $this->getAllCountries(),'preferred_choices' => array('Canada')
                ))*/
            ->add('address', 'text',array('required'=> false))
            ->add('neighborhood', 'text',array('required'=> false))
            ->add('city', 'text',array('required'=> true))
            ->add('state', 'text',array('required'=> false))
            ->add('country', 'choice', array('required' => true,
                'choices' => $this->getAllCountries(),'preferred_choices' => array('Canada')
                ))
            ->add('zip', 'text',array('required'=> false))
            ->add('phone', 'text',array('required'=> true))
            //->add('bank_account_id', 'text',array('required'=> true))
            ->add('file_scan_id', 'hidden')
            ->add('proof_of_identity_desc', 'hidden')

            ->getForm();
    }
    public function getName()
    {
        return 'investor_form';
    }

}
