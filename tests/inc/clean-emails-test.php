<?php

namespace Yoast\WP\Comment\Tests\Inc;

use Yoast\WP\Comment\Inc\Clean_Emails;
use Yoast\WP\Comment\Tests\TestCase;

/**
 * Test class to test the Clean_Emails class.
 */
class Clean_Emails_Test extends TestCase {

	/**
	 * Tests class constructor.
	 *
	 * @covers \Yoast\WP\Comment\Inc\Clean_Emails::__construct
	 */
	public function test__construct() {
		$instance = new Clean_Emails();

		$this->assertSame(
			10,
			\has_filter( 'comment_notification_text', [ $instance, 'comment_notification_text' ] ),
			'Filter for the "comment_notification_text" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_moderation_text', [ $instance, 'comment_moderation_text' ] ),
			'Filter for the "comment_moderation_text" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_notification_headers', [ $instance, 'comment_email_headers' ] ),
			'Filter for the "comment_notification_headers" not set or not at the correct priority'
		);
		$this->assertSame(
			10,
			\has_filter( 'comment_moderation_headers', [ $instance, 'comment_email_headers' ] ),
			'Filter for the "comment_moderation_headers" not set or not at the correct priority'
		);
	}

	/**
	 * Test setting the content type header for comment emails to "text/html".
	 *
	 * @dataProvider data_comment_email_headers
	 * @covers       \Yoast\WP\Comment\Inc\Clean_Emails::comment_email_headers
	 *
	 * @param mixed  $headers  The initial message headers.
	 * @param string $expected The expected function return value.
	 *
	 * @return void
	 */
	public function test_comment_email_headers( $headers, $expected ) {
		$instance = new Clean_Emails();
		$this->assertSame( $expected, $instance->comment_email_headers( $headers ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_comment_email_headers() {
		return [
			// Ensure the header is added when it is missing.
			'empty header string' => [
				'headers'  => '',
				'expected' => "Content-Type: text/html; charset=\"UTF-8\"\n",
			],

			// Ensure the header is changed to "text/html" when it exists and is set to "text/plain".
			'header string consisting of only a "text/plain" content type header' => [
				'headers'  => 'Content-Type: text/plain; charset="UTF-8"',
				'expected' => 'Content-Type: text/html; charset="UTF-8"',
			],
			'header string containing a "text/plain" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/plain; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/html; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],

			// Ensure that a header which is already correct is returned without changes.
			'header string consisting of only a "text/html" content type header' => [
				'headers'  => 'Content-Type: text/html; charset="UTF-8"',
				'expected' => 'Content-Type: text/html; charset="UTF-8"',
			],

			/*
			 * Ensure that if there is a content-type header, but it's not text/plain, the header is
			 * returned without changes.
			 * Unexpected headers like this could happen because another plugin does something spiffy
			 * with the comment email and we should try to avoid breaking their integration.
			 */
			'header string containing a non-standard "text" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/raw; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: text/raw; charset="UTF-8"
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],
			'header string containing a non-"text" content type header and other headers' => [
				'headers'  => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: message/partial;
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
				'expected' => 'From: "Blogname" <blogname@blogdomain.com>
Content-Type: message/partial;
Reply-To: "comment_author@theirdomain.com" <comment_author@theirdomain.com>
',
			],
		];
	}
}
