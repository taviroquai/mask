<?php

/**
 * Use Mask trait
 */
use Taviroquai\Mask\Mask;

/**
 * This partial view will be include in LayoutView
 *
 * @author mafonso
 */
class PartialView
{
    use Mask;
    
    /**
     * Set a description
     * 
     * @var string The description
     */
    protected $description = 'description';
    
}
