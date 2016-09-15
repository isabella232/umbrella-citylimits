<?php
/**
 * Description of Resumes
 *
 * @author greg
 * @package 
 */

class Wpjb_Module_Admin_Resumes extends Wpjb_Controller_Admin
{

    public function init()
    {
        $this->view->slot("logo", "candidates.png");
        $this->_virtual = array(
           "redirectAction" => array(
               "accept" => array("filter", "posted", "query"),
               "object" => "resumes"
           ),
           "addAction" => array(
               "form" => "Wpjb_Form_Resumes_Register",
               "info" => __("Candidate has been created.", "wpjobboard"),
               "error" => __("There are errors in your form.", "wpjobboard"),
               "url" => wpjb_admin_url("resumes", "edit", "%d")
           ),
           "editAction" => array(
               "form" => "Wpjb_Form_Admin_Resume",
               "info" => __("Resume has been saved.", "wpjobboard"),
               "error" => __("There are errors in the form.", "wpjobboard")
           ),
           "_multi" => array(
               "activate" => array(
                   "success" => __("Number of activated resumes: {success}", "wpjobboard")
               ),
               "deactivate" => array(
                   "success" => __("Number of deactivated resumes: {success}", "wpjobboard")
               ),
           ),
           "_multiDelete" => array(
               "model" => "Wpjb_Model_Resume"
           )
       );
    }

    public function indexAction()
    {        
        
        $screen = new Wpjb_Utility_ScreenOptions();
        $this->view->screen = $screen;
        $query = $this->_request->get("query");
        
        $this->view->rquery = $this->readableQuery($query);
        
        $param = $this->deriveParams($query, new Wpjb_Model_Resume);
        $param["filter"] = $this->_request->get("filter", "all");
        $param["page"] = (int)$this->_request->get("p", 1);
        $param["count"] = $screen->get("resume", "count", 20);
        
        $result = Wpjb_Model_ResumeSearch::search($param);
        
        $this->view->data = $result->resume;
        $this->view->filter = $param["filter"];
        $this->view->current = $param["page"];
        $this->view->total = $result->pages;
        $this->view->param = array("filter"=>$param["filter"], "query"=>$query);
        $this->view->query = $query;
        
        $stat = new stdClass();
        $stat->total = Wpjb_Model_ResumeSearch::search(array_merge($param, array("filter"=>"all", "count_only"=>1)));
        $stat->active = Wpjb_Model_ResumeSearch::search(array_merge($param, array("filter"=>"active", "count_only"=>1)));
        $stat->inactive = Wpjb_Model_ResumeSearch::search(array_merge($param, array("filter"=>"inactive", "count_only"=>1)));
        $this->view->stat = $stat;
        
    }

    protected function _multiActivate($id)
    {
        $object = new Wpjb_Model_Resume($id);
        $object->is_active = 1;
        $object->save();
        return true;
    }

    protected function _multiDeactivate($id)
    {
        $object = new Wpjb_Model_Resume($id);
        $object->is_active = 0;
        $object->save();
        return true;
    }
    
    public function slug($object = null)
    {
        global $wp_rewrite;

        $instance = Wpjb_Project::getInstance();
        $pedit = '<span id="editable-post-name" title="Click to edit this part of the permalink">%s</span>';
        $shortlink = null;
        
        if($object) {
            $permalink = $object->url();
        } else {
            $permalink = null;
            $object = new stdClass();
            $object->post_id = null;
        }

        if(!get_option('permalink_structure')) {
            $url = wpjr_link_to("resume", $object);
            $slug = null;
        } elseif($instance->env("uses_cpt")) {
            $post = get_post($object->post_id);
            
            if($post) {
                $shortlink = wp_get_shortlink($post->ID, 'post');
                $slug = $post->post_name;
            }
            
            $pstruct = $wp_rewrite->get_extra_permastruct("resume");
            $purl = home_url( user_trailingslashit($pstruct) );

            $url = str_replace("%resume%", $pedit, $purl);
        } else {
            $model = new Wpjb_Model_Resume();
            $model->candidate_slug = $pedit;
            $slug = $object->candidate_slug;
            $url = wpjr_link_to("resume", $model);
        }

        $this->view->url = sprintf($url, $slug);
        $this->view->permalink = $permalink;
        $this->view->shortlink = $shortlink;
        $this->view->slug = $slug;
    }
    
    
    public function editAction()
    {
        extract($this->_virtual[__FUNCTION__]);

        $id = $this->_request->getParam("id");
        $part = $this->_request->getParam("part");
        $sc = $this->_request->getParam("SaveClose");
        $diff = array();
        $resume = new Wpjb_Model_Resume($id);
        
        if(wpjb_conf("uses_cpt") && !$resume->post_id) {
            $resume->cpt();
        }
        
        $form = new $form($id);
        
        if($part) {
            $groups = array_keys($form->getGroups());
            $diff = array_diff($groups, (array)$part);
            $form->removeGroup($diff);
        } else {
            $this->slug($resume);
        }
        
        if($this->isPost()) {
            $isValid = $form->isValid($this->_request->getAll());
            if($isValid) {
                $this->_addInfo($info);
                $form->save();
                $id = $form->getId();
                $this->redirectIf($sc, wpjb_admin_url("resumes", "edit", $id));
            } else {
                $this->_addError($error);
            }
        }

        $this->redirectIf($part == "_internal", wpjb_admin_url("resumes", "edit", $id));
        
        $this->view->part = $part;
        $this->view->form = $form;
        $this->view->resume = $form->getObject();
        $this->view->user = new WP_User($this->view->resume->user_id);
    }
    
    public function adddetailAction()
    {
        if($this->_request->getParam("detail") == "experience") {
            $form = "Wpjb_Form_Resumes_Experience";
            $info = __("New work experience has been added.", "wpjobboard");
            $error = __("There are errors in your form", "wpjobboard");
            $title = __("Add work experience", "wpjobboard");
            
        } else {
            $form = "Wpjb_Form_Resumes_Education";
            $info = __("New education has been added.", "wpjobboard");
            $error = __("There are errors in your form", "wpjobboard");
            $title = __("Add education", "wpjobboard");
        }
        
        $rid = $this->_request->getParam("resume_id");
        $saveclose = $this->_request->getParam("SaveClose", false);
        
        $resume = new Wpjb_Model_Resume($rid);
        $this->redirectIf($resume->id<1, wpjb_admin_url("resumes"));
        
        $url = wpjb_admin_url("resumes", "edit", $rid);
        
        $form = new $form();
        $form->getElement("resume_id")->setValue($rid);
        $id = false;
        
        if($this->isPost()) {
            $isValid = $form->isValid($this->_request->getAll());
            if($isValid) {
                $this->_addInfo($info);
                $id = $form->save();
                if(!$id) {
                    $id = $form->getId();
                }
            } else {
                $this->_addError($error);
            }
        }

        $redir = wpjb_admin_url("resumes", "editdetail", $id, array(
            "detail"=>$this->_request->getParam("detail")
        ));

        
        $this->redirectIf($saveclose, sprintf($url, $rid));
        $this->redirectIf($id, $redir);
        $this->view->form = $form;
        
        $this->view->title = $title;
        $this->view->detail = $this->_request->getParam("detail");
        $this->view->resume = $resume;
        $this->view->user = new WP_User($resume->user_id);
        
        return "detail";
    }
    
    public function editdetailAction()
    {   
        $rid = $this->_request->getParam("id");
        $saveclose = $this->_request->getParam("SaveClose", false);
        
        $resume = new Wpjb_Model_ResumeDetail($rid);
        
        $this->redirectIf($resume->id<1, wpjb_admin_url("resumes"));
        $url = wpjb_admin_url("resumes", "edit", $resume->resume_id);
        
        if($resume->type == Wpjb_Model_ResumeDetail::EXPERIENCE) {
            $form = "Wpjb_Form_Resumes_Experience";
            $info = __("Work experience has been updated.", "wpjobboard");
            $error = __("There are errors in your form", "wpjobboard");
            $title = __("Edit work experience", "wpjobboard");
            
        } else {
            $form = "Wpjb_Form_Resumes_Education";
            $info = __("Education has been updated.", "wpjobboard");
            $error = __("There are errors in your form", "wpjobboard");
            $title = __("Edit education", "wpjobboard");
        }
        
        $form = new $form($resume->id);
        $form->removeElement("resume_id");
        $form->removeElement("type");
        $id = false;
        
        if($this->isPost()) {
            $isValid = $form->isValid($this->_request->getAll());
            if($isValid) {
                $this->_addInfo($info);
                $id = $form->save();
                if(!$id) {
                    $id = $form->getId();
                }
            } else {
                $this->_addError($error);
            }
        }
        
        $this->redirectIf($saveclose, sprintf($url, $rid));

        $this->view->form = $form;
        
        $this->view->title = $title;
        $this->view->detail = $this->_request->getParam("detail");
        $this->view->resume = new Wpjb_Model_Resume($resume->resume_id);
        $this->view->user = new WP_User($resume->user_id);
        
        return "detail-edit";
    }
    
    public function deletedetailAction()
    {
        $id = $this->_request->getParam("id");
        $object = new Wpjb_Model_ResumeDetail($id);
        $resume = $object->resume_id;
        
        if($object->id>0) {
            $object->delete();
            $this->_addInfo(__("Resume detail deleted", "wpjobboard"));
        } else {
            $this->_addError(__("Resume detail does not exist.", "wpjobboard"));
        }
        
        $this->redirect(wpjb_admin_url("resumes", "edit", $resume));
        
    }
    
    public function redirectAction()
    {
        if($this->_request->post("action") == "delete") {
            $param = array("users"=>$this->_request->post("item", array()));
            $url = wpjb_admin_url("resumes", "remove")."&".  http_build_query($param);
            wp_redirect($url);
            exit;
        }

        parent::redirectAction();
    }
    
    public function removeAction()
    {
        $query = new Daq_Db_Query();
        $query->from("Wpjb_Model_Resume t");
        $query->where("t.id IN(?)", $this->_request->get("users"));
        $this->view->list = $query->execute();
        $i = 0;
        
        if($this->isPost() && $this->_request->post("delete_option")) {
            
            $delete = Wpjb_Model_Resume::DELETE_FULL;
            if($this->_request->post("delete_option") == "partial") {
                $delete = Wpjb_Model_Resume::DELETE_PARTIAL;
            }

            foreach($this->_request->post("users", array()) as $id) {
                $resume = new Wpjb_Model_Resume($id);
                $resume->delete($delete);
                $i++;
                
                if($delete == Wpjb_Model_Resume::DELETE_FULL) {
                    $query = new Daq_Db_Query();
                    $query->from("Wpjb_Model_Application t");
                    $query->where("user_id = ?", $resume->user_id);
                    $list = $query->execute();


                    foreach($list as $app) {
                        if($this->_request->get("applications_option") == "delete") {
                            $app->delete();
                        } else {
                            $app->user_id = 0;
                            $app->save();
                        }
                    } //endforeach
                } // endif
                
            } // endforeach
            
            if($i > 0) {
                $msg = _n("One user deleted.", "%d users deleted.", $i, "wpjobboard");
                $this->_addInfo($msg);
            } else {
                $this->_addError(__("No users to delete", "wpjobboard"));
            }
            
            wp_redirect(wpjb_admin_url("resumes"));
            exit;
        }
    }

}

?>