<?php

namespace EmiliaProjects\WP\Comment\Inc;

/**
 * Registers the tasks for the Progress Planner.
 */
class Progress_Planner_Tasks {

    /**
     * Constructor.
     */
	public function __construct() {
        \add_filter( 'progress_planner_suggested_tasks_providers', [ $this, 'add_task_providers' ], 11, 1 );
	}

    /**
     * Adds the task providers to the Progress Planner.
     *
     * @param array $providers The array of task providers.
     *
     * @return array The array of task providers.
     */
    public function add_task_providers( $providers ) {
        // Remove the disable-comments provider - if you have this plugin installed, you don't need to see this task.
        foreach ( $providers as $key => $provider ) {
            if ( $provider->get_provider_id() === 'disable-comments' ) {
                unset( $providers[ $key ] );
            }
        }

        // Add the tasks.
        $providers[] = new Progress_Planner\Comment_Policy();
        $providers[] = new Progress_Planner\Comment_Redirect();
        $providers[] = new Progress_Planner\Comment_Moderation();

        return $providers;
    }

}