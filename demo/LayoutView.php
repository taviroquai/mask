<?php

/**
 * Require another view
 */
require_once 'PartialView.php';

/**
 * Use Mask trait
 */
use Taviroquai\Mask\Mask;

/**
 * Description of LayoutView
 *
 * @author mafonso
 */
class LayoutView
{
    use Mask;
    
    /**
     * Set a title
     * 
     * @var string The title
     */
    protected $title = 'titulo';
    
    /**
     * Set a description
     * 
     * @var string The description
     */
    protected $description = 'description';
    
    /**
     * Set a list of items
     * 
     * @var array The list of items
     */
    protected $items = array('one', 'two');
    
    /**
     * All logic MUST go here NOT in template
     * 
     * @return boolean The test description logic
     */
    public function hasLogic()
    {
        return true;
    }
    
    /**
     * Include another view
     * 
     * @return string The partial view
     */
    public function getSubView()
    {
        $view = new PartialView();
        return $view->mask('partial');
    }
}
