<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cpt
 *
 * @author Grzegorz
 */
class Wpjb_Utility_Cpt 
{
    public function init()
    {

        $args = array(
            'labels'        => array(
                "name" => __("Job", "wpjobboard"),
                "edit_item" => __("Edit Job", "wpjobboard"),
                "view_item" => __("View Job", "wpjobboard")
            ),
            'description'   => '',
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => false,
            'supports'      => array('title', 'comments'),
            'taxonomies'    => array( ),
            'has_archive'   => true,
            'rewrite'       => array(
                "slug"  => "job"
            )
        );
        register_post_type( 'job', apply_filters("wpjb_cpt_init", $args, "job") ); 
        
        $args = array(
            'labels'        => array(
                "name" => __("Candidate", "wpjobboard"),
                "edit_item" => __("Edit Candidate", "wpjobboard"),
                "view_item" => __("View Candidate", "wpjobboard")
            ),
            'description'   => '',
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => false,
            'supports'      => array('title', 'comments'),
            'taxonomies'    => array( ),
            'has_archive'   => true,
        );
        register_post_type( 'resume', apply_filters("wpjb_cpt_init", $args, "resume") ); 
        
        $args = array(
            'labels'        => array(
                "name" => __("Employer", "wpjobboard"),
                "edit_item" => __("Edit Employer", "wpjobboard"),
                "view_item" => __("View Employer", "wpjobboard")
            ),
            'description'   => '',
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => false,
            'supports'      => array('title', 'comments'),
            'taxonomies'    => array( ),
            'has_archive'   => true,
        );
        register_post_type( 'company', apply_filters("wpjb_cpt_init", $args, "company") ); 
    }
    
    public function link($object) 
    {
        $object->cpt();
        
    }
}

?>
