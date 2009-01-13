<?php
// BlogBridge Library
// Copyright (c) 2006 Salas Associates, Inc.  All Rights Reserved.
//
// Use, modification or copying prohibited unless appropriately licensed
// under an express agreement with Salas Associates, Inc.
//
// Contact: R. Pito Salas
// Mail To: support@blogbridge.com
//
// $Id: OPMLParser.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

class OPMLParser
{
    var $firstTag;
    var $opml;
    var $inHeader;
    var $inTitle;
    var $currentOutline;
    
    function parse($file, $import_structure = true, $suppress_empty_top = false)
    {
        $this->inHeader = false;
        $this->inTitle = false;
        $this->firstTag = true;
        $this->opml = null;
        $this->currentOutline = null;
        
        $parser = $this->_init_parser();
        
        if (!($fp = fopen($file, "r")))
        {
            die("could not open XML input");
        }
        
        while ($data = fread($fp, 4096))
        {
            if (!xml_parse($parser, $data, feof($fp)))
            {
                die(sprintf("XML error: %s at line %d",
                xml_error_string(xml_get_error_code($parser)),
                    xml_get_current_line_number($parser)));
            }
        }
        
        xml_parser_free($parser);
        
        return OPMLParser::post_process($this->opml, $import_structure, $suppress_empty_top);
    }

    function parseString($xml, $no_error = false, $import_structure = true, $suppress_empty_top = false)
    {
        $this->inHeader = false;
        $this->inTitle = false;
        $this->firstTag = true;
        $this->opml = null;
        $this->currentOutline = null;
        
        $parser = $this->_init_parser();
        
        if (!xml_parse($parser, $xml, true))
        {
            if (!$no_error) die(sprintf("XML error: %s at line %d",
            	xml_error_string(xml_get_error_code($parser)),
                xml_get_current_line_number($parser)));
        }
        
        xml_parser_free($parser);
        
        return OPMLParser::post_process($this->opml, $import_structure, $suppress_empty_top);
    }
    
    /** Initializes parser. */
    function _init_parser()
    {
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "_on_start_element", "_on_end_element");
        xml_set_character_data_handler($parser, '_on_character_data');
        
        return $parser;
    }
    
    /** Invoked on every start element. */
    function _on_start_element($parser, $tag, $attributes)
    {
        if ($this->firstTag && $tag == 'OPML')
        {
            $this->opml = $this->_create_outline();
            $this->currentOutline = &$this->opml;
        } else if (is_array($this->opml))
        {
            if ($tag == 'HEAD')
            {
                $this->inHeader = true;
            } else if ($tag == 'TITLE' && $this->inHeader)
            {
                $this->inTitle = true;
            } else if ($tag == 'OUTLINE')
            {
                $outline = $this->_create_outline($attributes);
                $outline['parent'] = &$this->currentOutline;
                $this->currentOutline = &$outline;
            }
        }
                
        $this->firstTag = false;
    }
    
    /** Invoked on every end element. */
    function _on_end_element($parser, $tag)
    {
        if ($tag == 'TITLE' && $this->inTitle)
        {
            $this->inTitle = false;
        } else if ($tag == 'HEAD' && $this->inHeader)
        {
            $this->inHeader = false;
        } else if ($tag == 'OUTLINE')
        {
            $parent = &$this->currentOutline['parent'];
            unset($this->currentOutline['parent']);
            $parent['children'][] = $this->currentOutline;
            $this->currentOutline = &$parent;
        }
    }
    
    /** Invoked on character data encounter. */
    function _on_character_data($parser, $data)
    {
        if ($this->inTitle) $this->opml['text'] = $data;
    }
    
    /** Creates outline structure. */
    function _create_outline($attrs = null)
    {
        $outline = $attrs;
        $outline['children'] = array();
        
        return $outline;
    }
    
    /**
     * Checks the options and modifies the OPML in accordance.
     */
    function post_process(&$opml, $import_structure, $suppress_empty_top)
    {
    	if (!$import_structure)
    	{
    		$opml = OPMLParser::pp_flat_structure($opml);
    	} else if ($suppress_empty_top)
    	{
    		$opml = OPMLParser::pp_remove_empty_top($opml);
    	}
    	
    	return $opml;
    }
    
    /**
     * Leaves only one level of outlines.
     */
    function pp_flat_structure(&$opml)
    {
    	$opml['children'] = OPMLParser::pp_get_children($opml);
    	
    	return $opml;
    }
    
    /**
     * Collects all children of the node. If the node is a leaf then it's inserted. 
     */
    function pp_get_children(&$outline)
    {
    	$children = array();
    	
    	if (count($outline['children']) == 0)
    	{
    		// Leaf: folder or feed
    		$children[] = $outline;
    	} else
    	{
    		// Node: folder
    		foreach ($outline['children'] as $child)
    		{
    			$children = array_merge($children, OPMLParser::pp_get_children($child));
    		}
    	}
    	
    	return $children;
    }
    
    /**
     * Removes all empty levels having only one sub-folder and nothing else.
     */
    function pp_remove_empty_top(&$opml)
    {
    	return count($opml['children']) == 1 
    		? OPMLParser::pp_remove_empty_top($opml['children'][0])
    		: $opml;
    }
}
?>
