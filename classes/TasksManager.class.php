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
// $Id: TasksManager.class.php,v 1.3 2007/07/19 16:01:46 alg Exp $
//

require_once 'Database.class.php';
require_once 'MetaDataUpdater.class.php';
require_once 'Task.class.php';
require_once 'Pinger.class.php';
require_once 'BackupsManager.class.php';
require_once 'AmazonThumbshooter.class.php';
require_once 'UpdatesChecker.class.php';
require_once 'LinksChecker.class.php';

/** Tasks management. */
class TasksManager
{
	/** Returns the list of tasks with properties. */
	function getTasks()
	{
		$tasks = array();
		
		$db = new Database();
		$res = $db->_query('SELECT t.*, seconds period FROM Task t, TaskPeriod p WHERE t.period_id=p.id');
		if ($res)
		{
			while ($row = mysql_fetch_assoc($res)) $tasks[] = TasksManager::_row2task($row);
			mysql_free_result($res); 
		}
		$db->disconnect();
		
		return $tasks;
	}
	
	/** Returns the list of periods. */
	function getTaskPeriods()
	{
		$periods = array();
		
		$db = new Database();
		$res = $db->_query('SELECT id, name FROM TaskPeriod');
		if ($res)
		{
			while ($row = mysql_fetch_assoc($res)) $periods[$row['id']] = $row['name'];
			mysql_free_result($res); 
		}
		$db->disconnect();
		
		return $periods;
	}
	
	/** Updates the list of tasks. */
	function updateTasks(&$tasks)
	{
		$db = new Database();
		foreach ($tasks as $name => $period_id)
		{
			$db->_query('UPDATE Task SET period_id=' . $period_id . ' WHERE name=\'' . $name . '\'');
		}
		$db->disconnect();
	}
	
	/** Checks if some of the tasks should be executed and executes them. */
	function runTasksIfTimeHasCome()
	{
		$tasks = TasksManager::getTasks();
		foreach ($tasks as $task)
		{
			TasksManager::runTaskIfTimeHasCome($task);
		}
	}
	
	/** Checks if time for the given task has come. */
	function runTaskIfTimeHasCome(&$task)
	{
		$next_exec = $task->last_exec + $task->period;
		$now = mktime();
		
		if ($next_exec < $now) TasksManager::runTask($task);
	}

	/** Runs task now and updates it's last execution time. */
	function runTask(&$task)
	{
		// Update last execution time
		$db = new Database();
		$db->_query('UPDATE Task SET last_exec=' . mktime() . ' WHERE name=\'' . $task->name . '\'');
		$db->disconnect();
		
		// Run task
		$method = 'task_' . $task->name;
		if (method_exists(new TasksManager(), $method))
		{
			call_user_func(array('TasksManager', $method));
		}		
	}
		
	/** Looks for a task with the given name and executes it. */
	function runTaskByName($name)
	{
		// Load task
		$db = new Database();
		$res = $db->_query('SELECT t.*, seconds period FROM Task t, TaskPeriod p WHERE t.period_id=p.id AND t.name=\'' . $name . '\'');
		if ($res)
		{
			if ($row = mysql_fetch_assoc($res))
			{
				$task = TasksManager::_row2task($row);		
				TasksManager::runTask($task);
			}
			mysql_free_result($res);
		}
	}
	
	// --- Tasks --------------------------------------------------------------
	
	/** Database backup routine. */
	function task_db_backup()
	{
		BackupsManager::backup();
	}
	
	/** OPML Folder check pinger. */
	function task_opml_folder_check()
	{
		$link = BASE_URL . '/opml_ping';
		Pinger::ping_link($link);
	}
	
	/** Cleans the cache of Amazon AST. */
	function task_amazon_cache_clean()
	{
		AmazonThumbshooter::cache_clean();
	}
	
	/** Updates meta-data of feeds. */
	function task_metadata_update()
	{
		MetaDataUpdater::run();
	}
	
	/** Checks the product site for available updates. */
	function task_check_for_updates()
	{
		UpdatesChecker::check();
	}
	
	/** Checks the next link from the database. */
	function task_check_links()
	{
		LinksChecker::check();
		LinksChecker::deliver_mail();
	}
	
	// ------------------------------------------------------------------------
	
	/** Converts task row to task object. */
	function _row2task(&$row)
	{
		$task = new Task();
		
		$task->name = $row['name'];
		$task->title = $row['title'];
		$task->description = $row['description'];
		$task->min_period_id = $row['min_period_id'];
		$task->period_id = $row['period_id'];
		$task->period = $row['period'];
		$task->last_exec = $row['last_exec'];
		
		return $task; 
	}
}
?>