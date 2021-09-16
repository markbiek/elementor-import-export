<?php

namespace ElImEx\Commands;

class ElementorExportCommand {
	const COMMAND_NAME = 'elementor-export';

	public function __invoke(array $args, array $assoc_args) {
	}

	public static function register() {
		if (defined('WP_CLI') && WP_CLI) {
			\WP_CLI::add_command(self::COMMAND_NAME, self::class);
		}
	}
}
