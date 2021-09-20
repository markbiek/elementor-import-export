# Elementor Import/Export Tool

**NOTE: This plugin is not affiliated with https://elementor.com/**

This plugin can be used to export Elementor post data from one environment, then easily import it on another.

**NOTE: Back up your database before performing any imports. This plugin is [not responsible](LICENSE) for any data loss.**

The plugin adds the following `wp` cli command-line commands:

## `wp elementor-export {post_id}`

Given a post/page ID, dump all Elementor-related post metadata to a file.

## `wp elementor-import {filename} {post_id}`

Give the filename of an elementor-export file and a post/page ID, import the Elementor-related post metdata.
