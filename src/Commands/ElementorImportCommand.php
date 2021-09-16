<?php

namespace ElImEx\Commands;

use Corcel\Model\Post;

class ElementorImportCommand {
	const COMMAND_NAME = 'elementor-import';

	/**
	 * Imports Elementor data for a post/page from a file
	 *
	 * ## OPTIONS
	 *
	 * <filename>
	 * : The filename to import
	 *
	 * <post_id>
	 * : The post/page id to import to
	 *
	 * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 */
	public function __invoke(array $args, array $assoc_args) {
		if (count($args) <= 0 || !file_exists($args[0])) {
			\WP_CLI::error('Please specify a valid import file.');
		}

		if (count($args) <= 0 || !is_numeric($args[1])) {
			\WP_CLI::error('Please specify a valid post id.');
		}

		$filename = $args[0];

		$postId = $args[1];
		$post = Post::find($postId);
		if (empty($post)) {
			\WP_CLI::error("A post/page with id $postId could not be found");
		}

		$data = unserialize(file_get_contents($filename));
		if (empty($data)) {
			\WP_CLI::error('The import file did not contain any data.');
		}

		$cleanData = [];
		foreach ($data as $key => $val) {
			if (strpos($key, 'elementor') === false) {
				continue;
			}

			$cleanData[$key] = $val;
		}
		if (empty($cleanData)) {
			\WP_CLI::error(
				'The import file did not contain any Elementor data.',
			);
		}

		$wpdb = $GLOBALS['wpdb'];

		foreach ($cleanData as $key => $val) {
			$sql = "SELECT COUNT(*) AS cnt FROM {$wpdb->prefix}postmeta WHERE post_id = {$postId} AND meta_key = '{$key}'";
			$row = $wpdb->get_row($sql);

			if ($row->cnt > 1) {
				throw new \Exception(
					"An unexpected error happened finding the meta_key $key (too many results)",
				);
			}

			if ($row->cnt == 1) {
				$sql = $wpdb->prepare(
					"UPDATE {$wpdb->prefix}postmeta SET meta_value = %s WHERE post_id = %d AND meta_key = %s",
					$val,
					$postId,
					$key,
				);
			} else {
				$sql = $wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value) VALUES (%d, %s, %s)",
				);
			}

			$wpdb->query($sql);
		}

		\WP_CLI::success("Imported all Elementor data to $filename.");
	}

	public static function register() {
		if (defined('WP_CLI') && WP_CLI) {
			\WP_CLI::add_command(self::COMMAND_NAME, self::class);
		}
	}
}
