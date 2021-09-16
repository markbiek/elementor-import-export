<?php

namespace ElImEx\Commands;

use Corcel\Model\Post;

class ElementorExportCommand {
	const COMMAND_NAME = 'elementor-export';

	/**
	 * Exports all Elementor data for a post/page to a file
	 *
	 * ## OPTIONS
	 *
	 * <post_id>
	 * : The ID of the post/page to export
	 *
	 * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 */
	public function __invoke(array $args, array $assoc_args) {
		if (count($args) <= 0 || !is_numeric($args[0])) {
			\WP_CLI::error('Please specify a valid post id.');
		}

		$postId = $args[0];

		$post = Post::find($postId);
		if (empty($post)) {
			\WP_CLI::error("A post/page with id $postId could not be found");
		}

		$wpdb = $GLOBALS['wpdb'];
		$sql = <<<EOT
		SELECT
			*
		FROM
			gid_postmeta
		WHERE
			post_id = %d
			AND meta_key LIKE '%%elementor%%'
		ORDER BY
			meta_key
		EOT;
		$sql = $wpdb->prepare($sql, $postId);
		$ret = $wpdb->get_results($sql);

		if (count($ret) <= 0) {
			\WP_CLI::error(
				"The post/page $postId doesn't appear to be edited with Elementor",
			);
		}

		$filename = "elementor-export-$postId.dat";

		$data = [];
		foreach ($ret as $row) {
			$data[$row->meta_key] = $row->meta_value;
		}

		file_put_contents($filename, serialize($data));

		\WP_CLI::success("Post $postId has been exported to $filename");
	}

	public static function register() {
		if (defined('WP_CLI') && WP_CLI) {
			\WP_CLI::add_command(self::COMMAND_NAME, self::class);
		}
	}
}
