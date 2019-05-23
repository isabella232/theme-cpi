<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 */
namespace CPI\Blocks\EmailSignUp;

use Timber\Timber;

class EmailSignUp
{
    /**
     * Constructor
     */
    public function __construct()
    {
                add_action('acf/init', array($this,'createEmailSignUpBlock'));
    }
     /**
      * Uses ACF function to register custom blocks
      *
      * @return void
      */
    public function createEmailSignUpBlock()
    {
        // check function exists
        if (function_exists('acf_register_block')) {
         // register RelatedArticles block
            acf_register_block(
                array(
                    'name'            => 'emailSignUp',
                    'title'           => __('Email Sign-up'),
                    'description'     => __('A custom block for inserting a call to action with an email sign-up.'),
                    'render_callback' => array($this, 'renderEmailSignUpBlock'),
                    'category'        => 'widgets',
                    'icon'            => array('background' => '#ecf6f6', 'src' => 'email'),
                    'keywords'        => array('email', 'sign', 'up'),
                    'mode'            => 'edit'
                )
            );
        }
    }
     /**
      * Get the Related Articles text and related articles,
      * and then render corrosponding template
      *
      * @param array  $block      The block settings and attributes.
      * @param string $content    The block content (empty content).
      * @param bool   $is_preview True during AJAX preview.
      *
      * @return void
      */
    public function renderEmailSignUpBlock($block, $content, $is_preview)
    {
        $templates = ['templates/components/article-email-signup.twig', 'templates/components/mailchimp-form.twig'];


        $context['email_signup_headline'] = get_field('email_signup_headline');
        $context['email_signup_text'] = get_field('email_signup_text');

         // ob_start();
        if ($is_preview) {
            echo "Preview mode is not supported for related articles. Please change to Edit mode by clicking the pencil icon in the toolbar above.";
        } else {
            Timber::render($templates, $context);
        }
    }
}
