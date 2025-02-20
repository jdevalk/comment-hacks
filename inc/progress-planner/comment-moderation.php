<?php

namespace EmiliaProjects\WP\Comment\Inc\Progress_Planner;

use EmiliaProjects\WP\Comment\Inc\Hacks;

/**
 * Task for the comment moderation.
 */
class Comment_Moderation extends \Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Repetitive\Repetitive {

	/**
	 * The provider ID.
	 *
	 * @var string
	 */
	const ID = 'ch-comment-moderation';

	/**
	 * The provider type. This is used to determine the type of task.
	 *
	 * @var string
	 */
	const TYPE = 'maintenance';

    /**
	 * Holds our options.
	 *
	 * @var string[]
	 */
	private array $options;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->options = Hacks::get_options();
    }

	/**
	 * Check if the task should be added.
	 *
	 * @return bool
	 */
	public function should_add_task() {
		$comments = \get_comments( [
			'status' => 'hold',
			'count'  => true,
		] );

		return $comments > 0;
	}

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id = '' ) {

		if ( ! $task_id ) {
			$task_id = $this->get_provider_id() . '-' . \gmdate( 'YW' );
		}

		return [
			'task_id'      => $task_id,
			'title'        => \esc_html__( 'Moderate comments', 'comment-hacks' ),
			'parent'       => 0,
			'priority'     => 'high',
			'type'         => $this->get_provider_type(),
			'points'       => 1,
			'url'          => $this->capability_required() ? \esc_url( \admin_url( 'edit-comments.php?comment_status=moderated' ) ) : '',
			'description'  => '<p>' . \esc_html__( 'Moderate comments to make sure they are not spam.', 'comment-hacks' ) . '</p>',
		];
	}
}