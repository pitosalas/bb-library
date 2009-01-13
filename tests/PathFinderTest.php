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
// $Id: PathFinderTest.php,v 1.3 2007/01/03 13:53:26 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Database.class.php';
require_once $classes . 'PathFinder.class.php';

class FoldersTest extends DefaultTestCase
{
	var $db;
	var $folders, $items;
	var $seq;
	
	function setUp()
	{
		$this->db = new Database();
		$this->folders = array();
		$this->items = array();
		$this->seq = 0;
	}
	
	function tearDown()
	{
		// Remove newly created folders
		foreach ($this->folders as $fid)
		{
			$this->db->deleteFolder($fid);
		}
		
		$this->db->disconnect();
	}
	
	/**
	 * Creates new folder that sees other folders.
	 * Returns the id of this new folder.
	 */
	function folder($parents)
	{
		$this->seq++;
		$num = $this->seq;
		
		// Create a folder
		$folder = new Folder(ADMIN_USER_ID, "pathfinder_test $num");
		$shortcuts = null;
		if ($parents != null)
		{
			$shortcuts = is_array($parents) ? $parents : array($parents);
		}
		$tags = null;
		$folder = $this->db->addFolder($folder, $shortcuts, $tags);
		 
		// Record the ID of this folder for later automatic removal 
		$this->folders[] = $folder->id;
		
		return $folder->id;
	}
	
	/**
	 * Creates a new item that sees other folders.
	 * Returns the id of this new item.
	 */
	function item($parents)
	{
		$this->seq++;
		$num = $this->seq;
		
		// Create an item
		$item = new Item(ADMIN_USER_ID, "pathfinder_test $num");
		$item->type_id = 1;
		$shortcuts = null;
		if ($parents != null)
		{
			$shortcuts = is_array($parents) ? $parents : array($parents);
		}
		$tags = null;
		$item = $this->db->addItem($item, $shortcuts, $tags);
		 
		// Record the ID of this item for later automatic removal 
		$this->items[] = $item->id;
		
		return $item->id;
	}
	
	function assertPath($path, $target)
	{
		if ($target == null)
		{
			$this->assertNull($path);
		} else
		{			
			$this->assertNotNull($path);
			$this->assertEqual(count($target), count($path));
			for ($i = 0; $i < count($target); $i++)
			{
				$t = $target[$i];
				$p = $path[$i];
				
				if ($t != $p)
				{
					$this->assertEqual($t, $p);
					break;
				}
			}
		}
	}
	
	/**
	 * Tests reporting no link between two folders.
	 */
	function test_folder_no_link()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder(ROOT_FOLDER_ID);
		
		$this->assertNull(PathFinder::find_path($f1, $f2),
			'There\'s no link between two folders.'); 
	}
	
	/**
	 * Tests reporting no link between two items.
	 */
	function test_item_no_link()
	{
		$i1 = $this->item(ROOT_FOLDER_ID);
		$f2 = $this->folder(ROOT_FOLDER_ID);
		
		$this->assertNull(PathFinder::find_path($i1, $f2, false),
			'There\'s no link between the item and the other folder.'); 
	}
	
	/**
	 * Tests reporting one step link between two folders.
	 */
	function test_folder_one_step()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		
		$path = PathFinder::find_path($f1, ROOT_FOLDER_ID);

		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1));		
	}

	/**
	 * Tests reporting one step link between an item and a folder.
	 */
	function test_item_one_step()
	{
		$i1 = $this->item(ROOT_FOLDER_ID);
		
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);

		$this->assertPath($path, array(ROOT_FOLDER_ID, $i1));		
	}
	
	/**
	 * Tests reporting simple two-step chain.
	 */
	function test_folder_two_step()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		
		$path = PathFinder::find_path($f2, ROOT_FOLDER_ID);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $f2));		
	}
	
	/**
	 * Tests reporting simple two-step chain.
	 */
	function test_item_two_step()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$i1 = $this->item($f1);
		
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $i1));		
	}
	
	/**
	 * Tests finding simple short path. The layout:
	 * 
	 * Home --- F1 --- F2 --- F4
	 *      \               /
	 *       -- F3 ---------
	 */
	function test_folder_simple_shortpath()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f3 = $this->folder(ROOT_FOLDER_ID);
		$f4 = $this->folder(array($f2, $f3));
				
		$path = PathFinder::find_path($f4, ROOT_FOLDER_ID);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f3, $f4));		
	}
	
	/**
	 * Tests finding simple short path. The layout:
	 * 
	 * Home --- F1 --- F2 --- I1
	 *      \               /
	 *       -- F3 ---------
	 */
	function test_item_simple_shortpath()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f3 = $this->folder(ROOT_FOLDER_ID);
		$i1 = $this->item(array($f2, $f3));
				
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f3, $i1));		
	}
	
	/**
	 * Tests finding complex short path. The layout:
	 *
	 *                     -- F3 --- F4 --
	 *                    /               \
	 * Home --- F1 --- F2 ----------------- F6
	 *      \                             /
	 *       -- F5 -----------------------
	 */
	function test_folder_complex_shortpath()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f3 = $this->folder($f2);
		$f4 = $this->folder($f3);
		$f5 = $this->folder(ROOT_FOLDER_ID);
		$f6 = $this->folder(array($f2, $f4, $f5));
		
		$path = PathFinder::find_path($f6, ROOT_FOLDER_ID);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f5, $f6));		
	}
	
	/**
	 * Tests finding complex short path. The layout:
	 *
	 *                     -- F3 --- F4 --
	 *                    /               \
	 * Home --- F1 --- F2 ----------------- I1
	 *      \                             /
	 *       -- F5 -----------------------
	 */
	function test_item_complex_shortpath()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f3 = $this->folder($f2);
		$f4 = $this->folder($f3);
		$f5 = $this->folder(ROOT_FOLDER_ID);
		$i1 = $this->item(array($f2, $f4, $f5));
		
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f5, $i1));		
	}

	/**
	 * Tests finding complex short path. The layout:
	 *
	 *                     -- F3 --- F4 --
	 *                    /   /            \
	 * Home --- F1 --- F2 -- / ------------ F6
	 *      \               /
	 *       -- F5 --------
	 */
	function test_folder_complex_shortpath2()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f5 = $this->folder(ROOT_FOLDER_ID);
		$f3 = $this->folder(array($f2, $f5));
		$f4 = $this->folder($f3);
		$f6 = $this->folder(array($f2, $f4));
		
		$path = PathFinder::find_path($f6, ROOT_FOLDER_ID);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $f2, $f6));		
	}

	/**
	 * Tests finding complex short path. The layout:
	 *
	 *                     -- F3 --- F4 --
	 *                    /   /            \
	 * Home --- F1 --- F2 -- / ------------ I1
	 *      \               /
	 *       -- F5 --------
	 */
	function test_item_complex_shortpath2()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		$f5 = $this->folder(ROOT_FOLDER_ID);
		$f3 = $this->folder(array($f2, $f5));
		$f4 = $this->folder($f3);
		$i1 = $this->item(array($f2, $f4));
		
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $f2, $i1));		
	}
	
	/**
	 * Tests reporting visibility of nodes from other nodes.
	 */
	function test_folder_visible_from()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		
		$vf2 = PathFinder::visible_from($f2);
		$this->assertEqual(1, count($vf2));
		$this->assertEqual($f1, $vf2[0]);
		
		$vf1 = PathFinder::visible_from($f1);
		$this->assertEqual(1, count($vf1));
		$this->assertEqual(ROOT_FOLDER_ID, $vf1[0]);
	}
	
	/**
	 * Tests reporting visibility of nodes from other nodes.
	 */
	function test_item_visible_from()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$i1 = $this->item($f1);
		
		$vf2 = PathFinder::visible_from($i1, false);
		$this->assertEqual(1, count($vf2));
		$this->assertEqual($f1, $vf2[0]);
	}
	
	/**
	 * Tests how the records make their way to cache.
	 */
	function test_folder_cache_write()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$f2 = $this->folder($f1);
		
		// Initiate search that will create records in the cache table
		$path = PathFinder::find_path($f2, ROOT_FOLDER_ID);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $f2));		
		
		$path = PathFinder::lookup_shortest_path($f2, ROOT_FOLDER_ID);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $f2));		

		$path = PathFinder::lookup_shortest_path($f1, ROOT_FOLDER_ID);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1));		
	}
	
	/**
	 * Tests how the records make their way to cache.
	 */
	function test_item_cache_write()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		$i1 = $this->item($f1);
		
		// Initiate search that will create records in the cache table
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $i1));		
		
		$path = PathFinder::lookup_shortest_path($i1, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1, $i1));		
	}
	
	/**
	 * Tests how cache is invalidated.
	 */
	function test_folder_cache_invalidation()
	{
		$f1 = $this->folder(ROOT_FOLDER_ID);
		
		// Initiate search that will create records in the cache table
		$path = PathFinder::find_path($f1, ROOT_FOLDER_ID);
		
		$path = PathFinder::lookup_shortest_path($f1, ROOT_FOLDER_ID);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $f1));
		
		PathFinder::invalidate_cache();
		$path = PathFinder::lookup_shortest_path($f1, ROOT_FOLDER_ID);
		$this->assertNull($path);
	}
	
	/**
	 * Tests how cache is invalidated.
	 */
	function test_item_cache_invalidation()
	{
		$i1 = $this->item(ROOT_FOLDER_ID);
		
		// Initiate search that will create records in the cache table
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		
		$path = PathFinder::lookup_shortest_path($i1, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $i1));
		
		PathFinder::invalidate_cache(false);
		$path = PathFinder::lookup_shortest_path($i1, ROOT_FOLDER_ID, false);
		$this->assertNull($path);
	}
	
	/**
	 * Tests how cache is invalidated selectively.
	 */
	function test_item_selective_cache_invalidation()
	{
		$i1 = $this->item(ROOT_FOLDER_ID);
		$i2 = $this->item(ROOT_FOLDER_ID);
		
		// Initiate search that will create records in the cache table
		$path = PathFinder::find_path($i1, ROOT_FOLDER_ID, false);
		$path = PathFinder::find_path($i2, ROOT_FOLDER_ID, false);
		
		$path = PathFinder::lookup_shortest_path($i1, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $i1));
		$path = PathFinder::lookup_shortest_path($i2, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $i2));
		
		PathFinder::invalidate_cache(false, $i1);
		$path = PathFinder::lookup_shortest_path($i1, ROOT_FOLDER_ID, false);
		$this->assertNull($path);
		$path = PathFinder::lookup_shortest_path($i2, ROOT_FOLDER_ID, false);
		$this->assertPath($path, array(ROOT_FOLDER_ID, $i2));
	}
}
?>
