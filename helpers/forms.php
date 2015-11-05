<?php
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

function sas_loadForm($formName, Silex\Application $app) {
    switch ($formName) {
        case 'Todo':
            $data = array(
                'Title' => 'Your next Todo',
                'Body' => 'I have to get some bananas for the monkeys...',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('Title', 'text',
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
                ->add('Body', 'textarea',
                	array(
				        'attr' => 	array(
				                		'class' => 'form-control'
				                	),
				        'constraints' => array(
				        	new Assert\NotBlank(),
				        )
				    )
				)
                ->getForm();
        break;
        case 'Contact':
            $data = array(
                'title' => 'Contact Us',
                'body' => 'This is a message to you guys...',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('title', 'text', array('attr' => array('class' => 'form-control')))
                ->add('body', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => 5)))
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