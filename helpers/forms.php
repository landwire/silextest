<?php
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

function sas_loadForm($formName, $data, Silex\Application $app) {
    switch ($formName) {
        case 'Todo':
            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('title', 'text',
                	array(
				        'attr' => 	array(
				                		'class' => 'form-control'
				                	),
				        'constraints' => array(
				        	new Assert\NotBlank(), 
				        	new Assert\Length(array('min' => 5))
				        )
				    )
                )
                ->add('body', 'textarea',
                	array(
				        'attr' => 	array(
				                		'class' => 'form-control',
                                        'rows'  => 5
				                	),
				        'constraints' => array(
				        	new Assert\NotBlank(),
				        )
				    )
				)
                ->add('save', 'submit', array('label' => 'Create Todo'))
                ->getForm();
        break;
        case 'Edit':
            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('title', 'text',
                    array(
                        'attr' =>   array(
                                        'class' => 'form-control'
                                    ),
                        'constraints' => array(
                            new Assert\NotBlank(), 
                            new Assert\Length(array('min' => 5))
                        )
                    )
                )
                ->add('body', 'textarea',
                    array(
                        'attr' =>   array(
                                        'class' => 'form-control',
                                        'rows'  => 5
                                    ),
                        'constraints' => array(
                            new Assert\NotBlank(),
                        )
                    )
                )
                ->add('save', 'submit', array('label' => 'Update Todo'))
                ->getForm();
        break;
        case 'Contact':
            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('title', 'text', array('attr' => array('class' => 'form-control')))
                ->add('body', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => 5)))
                ->add('save', 'submit', array('label' => 'Send Request'))
                ->getForm();
        break;
    }

    // is that check enough????
    if ($form) {
        return $form;
    }
    else {
        return false;
    }
}