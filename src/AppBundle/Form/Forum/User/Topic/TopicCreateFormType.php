<?php

/*
 * This file is part of the CCDNForum ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrowdValley\Bundle\AppBundle\Form\Forum\User\Topic;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @category CCDNForum
 * @package  ForumBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNForumForumBundle
 *
 */
class TopicCreateFormType extends AbstractType
{
    /**
     *
     * @access protected
     * @var string $topicClass
     */
    protected $topicClass;

    /**
     *
     * @access public
     * @var string $topicClass
     */
    public function __construct($topicData = null)
    {
        $this->topicData = $topicData;
    }

    /**
     *
     * @access public
     * @param FormBuilderInterface $builder, array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('board', 'choice',
                array(
                    'choices'            => $this->topicData,
                    'label'              => 'board.label',
                    'translation_domain' => 'CVNetworkBundle',
                )
            )
            ->add('title', null,
                array(
                    'required'           => true,
                    'label'              => 'topic.title-label',
                    'translation_domain' => 'CVNetworkBundle'
                )
            )
        ;
    }

    /**
     *
     * @access public
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
//            'data_class'         => $this->topicClass,
            'csrf_protection'    => true,
            'csrf_field_name'    => '_token',
            // a unique key to help generate the secret token
            'intention'          => 'forum_topic_create_item',
            'validation_groups'  => array('forum_topic_create', 'forum_post_create'),
            'boards'             => array(),
        ));
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Topic';
    }
}