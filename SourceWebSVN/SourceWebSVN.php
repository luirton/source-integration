<?php
# Copyright (C) 2008-2009 John Reese, LeetCode.net
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.

if ( false === include_once( config_get( 'plugin_path' ) . 'Source/MantisSourcePlugin.class.php' ) ) {
	return;
}

class SourceWebSVNPlugin extends MantisSourcePlugin {
	function register() {
		$this->name = lang_get( 'plugin_SourceWebSVN_title' );
		$this->description = lang_get( 'plugin_SourceWebSVN_description' );
		$this->page = 'config_page';

		$this->version = '0.13';
		$this->requires = array(
			'MantisCore' => '1.2.0',
			'Source' => '0.13',
		);

		$this->author = 'John Reese';
		$this->contact = 'jreese@leetcode.net';
		$this->url = 'http://leetcode.net';
	}

	function config() {
		return array(
			'svnpath' => '',
		);
	}

	function get_types( $p_event ) {
		return array( 'svn' => lang_get( 'plugin_SourceWebSVN_svn' ) );
	}

	function show_type( $p_event, $p_type ) {
		if ( 'svn' == $p_type ) {
			return lang_get( 'plugin_SourceWebSVN_svn' );
		}
	}

	function show_changeset( $p_event, $p_repo, $p_changeset ) {
		if ( 'svn' == $p_repo->type ) {
			return "$p_changeset->branch r$p_changeset->revision";
		}
	}

	function show_file( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'svn' == $p_repo->type ) {
			return $p_file->action . ' - ' . $p_file->filename;
		}
	}

	function url_repo( $p_event, $p_repo, $p_changeset=null ) {
		if ( 'svn' == $p_repo->type ) {
			$t_rev = '';
			$t_path = '';

			if ( !is_null( $p_changeset ) ) {
				$t_rev = '&rev=' . urlencode( $p_changeset->revision );
			}
			if ( !is_blank( $p_repo->info['websvn_path'] ) ) {
				$t_path = '&path=' . urlencode( $p_repo->info['websvn_path'] );
			}
			return $p_repo->info['websvn_url'] . 'listing.php?repname=' . urlencode( $p_repo->info['websvn_name'] ) . "$t_path$t_rev&sc=1";
		}
	}

	function url_changeset( $p_event, $p_repo, $p_changeset ) {
		if ( 'svn' == $p_repo->type ) {
			return $this->url_repo( $p_event, $p_repo, $p_changeset );
		}
	}

	function url_file( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'svn' == $p_repo->type ) {
			if ( $p_file->action == 'D' ) {
				return '';
			}
			return $p_repo->info['websvn_url'] . 'filedetails.php?repname=' . urlencode( $p_repo->info['websvn_name'] ) .
				'&rev=' . urlencode( $p_changeset->revision ) . '&path=' . urlencode( $p_file->filename ) . '&sc=1';
		}
	}

	function url_diff( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'svn' == $p_repo->type ) {
			if ( $p_file->action == 'D' || $p_file->action == 'A' ) {
				return '';
			}
			return $p_repo->info['websvn_url'] . 'diff.php?repname=' . urlencode( $p_repo->info['websvn_name'] ) .
				'&rev=' . urlencode( $p_changeset->revision ) . '&path=' . urlencode( $p_file->filename ) . '&sc=1';
		}
	}

	function update_repo_form( $p_event, $p_repo ) {
		if ( 'svn' != $p_repo->type ) {
			return;
		}

		$t_svn_username = isset( $p_repo->info['svn_password'] ) ? $p_repo->info['svn_password'] : '';
		$t_svn_password = isset( $p_repo->info['svn_username'] ) ? $p_repo->info['svn_username'] : '';
		$t_url = isset( $p_repo->info['websvn_url'] ) ? $p_repo->info['websvn_url'] : '';
		$t_name = isset( $p_repo->info['websvn_name'] ) ? $p_repo->info['websvn_name'] : '';
		$t_path = isset( $p_repo->info['websvn_path'] ) ? $p_repo->info['websvn_path'] : '';
		$t_standard_repo = isset( $p_repo->info['standard_repo'] ) ? $p_repo->info['standard_repo'] : '';
		$t_trunk_path = isset( $p_repo->info['trunk_path'] ) ? $p_repo->info['trunk_path'] : '';
		$t_branch_path = isset( $p_repo->info['branch_path'] ) ? $p_repo->info['branch_path'] : '';
		$t_ignore_paths = isset( $p_repo->info['ignore_paths'] ) ? $p_repo->info['ignore_paths'] : '';

?>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_svn_username' ) ?></td>
<td><input name="svn_username" maxlength="250" size="40" value="<?php echo string_attribute( $t_svn_username ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_svn_password' ) ?></td>
<td><input name="svn_password" maxlength="250" size="40" value="<?php echo string_attribute( $t_svn_password ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_websvn_url' ) ?></td>
<td><input name="websvn_url" maxlength="250" size="40" value="<?php echo string_attribute( $t_url ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_websvn_name' ) ?></td>
<td><input name="websvn_name" maxlength="250" size="40" value="<?php echo string_attribute( $t_name ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_websvn_path' ) ?></td>
<td><input name="websvn_path" maxlength="250" size="40" value="<?php echo string_attribute( $t_path ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_standard_repo' ) ?></td>
<td><input name="standard_repo" type="checkbox" <?php echo ($t_standard_repo ? 'checked="checked"' : '') ?>/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_trunk_path' ) ?></td>
<td><input name="trunk_path" maxlength="250" size="40" value="<?php echo string_attribute( $t_trunk_path ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_branch_path' ) ?></td>
<td><input name="branch_path" maxlength="250" size="40" value="<?php echo string_attribute( $t_branch_path ) ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceWebSVN_ignore_paths' ) ?></td>
<td><input name="ignore_paths" type="checkbox" <?php echo ($t_ignore_paths ? 'checked="checked"' : '') ?>/></td>
</tr>
<?php
	}

	function update_repo( $p_event, $p_repo ) {
		if (  'svn' != $p_repo->type ) {
			return;
		}

		$p_repo->info['svn_username'] = gpc_get_string( 'svn_username' );
		$p_repo->info['svn_password'] = gpc_get_string( 'svn_password' );
		$p_repo->info['websvn_url'] = gpc_get_string( 'websvn_url' );
		$p_repo->info['websvn_name'] = gpc_get_string( 'websvn_name' );
		$p_repo->info['websvn_path'] = gpc_get_string( 'websvn_path' );
		$p_repo->info['standard_repo'] = gpc_get_bool( 'standard_repo', false );
		$p_repo->info['trunk_path'] = gpc_get_string( 'trunk_path' );
		$p_repo->info['branch_path'] = gpc_get_string( 'branch_path' );
		$p_repo->info['ignore_paths'] = gpc_get_bool( 'ignore_paths', false );

		return $p_repo;
	}

	function commit( $p_event, $p_repo, $p_data ) {
		if ( 'svn' != $p_repo->type ) {
			return null;
		}

		if ( preg_match( '/(\d+)/', $p_data, $p_matches ) ) {
			$svn = $this->svn_call( $p_repo );

			$t_url = $p_repo->url;
			$t_revision = $p_matches[1];
			$t_svnlog = explode( "\n", shell_exec( "$svn log -v $t_url -r$t_revision" ) );

			if ( SourceChangeset::exists( $p_repo->id, $t_revision ) ) {
				echo "Revision $t_revision already committed!\n";
				return null;
			}

			return $this->process_svn_log( $p_repo, $t_svnlog );
		}
	}

	function import_full( $p_event, $p_repo ) {
		if ( 'svn' != $p_repo->type ) {
			return;
		}

		$this->check_svn();
		$svn = $this->svn_call( $p_repo );

		$t_changeset_table = plugin_table( 'changeset', 'Source' );

		$t_max_query = "SELECT revision FROM $t_changeset_table
						WHERE repo_id=" . db_param() . '
						ORDER BY timestamp DESC';
		$t_db_revision = db_result( db_query_bound( $t_max_query, array( $p_repo->id ), 1 ) );

		$t_url = $p_repo->url;
		$t_rev = ( false === $t_db_revision ? 0 : $t_db_revision + 1 );
		$t_svnlog = explode( "\n", shell_exec( "$svn log -v -r $t_rev:HEAD --limit 200 $t_url" ) );

		return $this->process_svn_log( $p_repo, $t_svnlog );
	}

	function import_latest( $p_event, $p_repo ) {
		if ( 'svn' != $p_repo->type ) {
			return;
		}

		return $this->import_full( $p_event, $p_repo );
	}

	function check_svn() {
		$svn = $this->svn_call();

		if ( is_blank( shell_exec( "$svn help" ) ) ) {
			trigger_error( ERROR_GENERIC, ERROR );
		}
	}

	function svn_call( $p_repo=null ) {
		static $s_call;

		# Generate, validate, and cache the SVN binary path
		if ( is_null( $s_call ) ) {
			$t_path = plugin_config_get( 'svnpath' );
			if ( !is_blank( $t_path ) && is_dir( $t_path ) ) {
				$s_call = $t_path . DIRECTORY_SEPARATOR . 'svn';

				if ( !is_file( $s_call ) || !is_executable( $s_call ) ) {
					$s_call = 'svn';
				}

			} else {
				$s_call = 'svn';
			}
		}

		# If not given a repo, just return the base SVN binary
		if ( is_null( $p_repo ) ) {
			return $s_call;
		}

		# With a repo, add arguments for repo info
		$t_call = $s_call . ' --non-interactive';
		$t_username = $p_repo->info['svn_username'];
		$t_password = $p_repo->info['svn_password'];

		if ( !is_blank( $t_username ) ) {
			$t_call .= ' --username ' . $t_username;
		}
		if ( !is_blank( $t_password ) ) {
			$t_call .= ' --password ' . $t_password;
		}

		# Done
		return $t_call;
	}

	function process_svn_log( $p_repo, $p_svnlog ) {
		$t_state = 0;
		$t_svnline = str_pad( '', 72, '-' );

		$t_changesets = array();
		$t_changeset = null;
		$t_comments = '';
		$t_count = 0;

		$t_trunk_path = $p_repo->info['trunk_path'];
		$t_branch_path = $p_repo->info['branch_path'];
		$t_ignore_paths = $p_repo->info['ignore_paths'];

		foreach( $p_svnlog as $t_line ) {

			# starting state, do nothing
			if ( 0 == $t_state ) {
				if ( $t_line == $t_svnline ) {
					$t_state = 1;
				}

			# Changeset info
			} elseif ( 1 == $t_state && preg_match( '/^r([0-9]+) \| ([^|]+) \| ([0-9\-]+) ([0-9:]+)/', $t_line, $t_matches ) ) {
				if ( !is_null( $t_changeset ) && !is_blank( $t_changeset->branch ) ) {
					$t_changeset->save();
					$t_changesets[] = $t_changeset;
				}

				$t_changeset = new SourceChangeset( $p_repo->id, $t_matches[1], '', $t_matches[3] . ' ' . $t_matches[4], $t_matches[2], '' );

				$t_state = 2;

			# Changed paths
			} elseif ( 2 == $t_state ) {
				if ( strlen( $t_line ) == 0 ) {
					$t_state = 3;
				} else {
					if ( preg_match( '/^\s+([a-zA-Z])\s+(.+)/', $t_line, $t_matches ) ) {
						switch( $t_matches[1] ) {
							case 'A': $t_action = 'add'; break;
							case 'D': $t_action = 'rm'; break;
							case 'M': $t_action = 'mod'; break;
							case 'R': $t_action = 'mv'; break;
							default: $t_action = $t_matches[1];
						}

						$t_file = new SourceFile( $t_changeset->id, '', trim( $t_matches[2] ), $t_action );
						$t_changeset->files[] = $t_file;

						# Branch-checking
						if ( is_blank( $t_changeset->branch) ) {
							# Look for standard trunk/branches/tags information
							if ( $p_repo->info['standard_repo'] ) {
								if ( preg_match( '/\/(?:(trunk)|(?:branches|tags)\/([^\/]+))/', $t_file->filename, $t_matches ) ) {
									if ( 'trunk' == $t_matches[1] ) {
										$t_changeset->branch = 'trunk';
									} else {
										$t_changeset->branch = $t_matches[2];
									}
								}
							} else {
								# Look for non-standard trunk path
								if ( !is_blank( $t_trunk_path ) && preg_match( '@^' . $t_trunk_path . '@i', $t_file->filename ) ) {
									$t_changeset->branch = 'trunk';

								# Look for non-standard branch path
								} else if ( !is_blank( $t_branch_path ) && preg_match( '@^' . $t_branch_path . '([^\/]+)@i', $t_file->filename, $t_matches ) ) {
									$t_changeset->branch = $t_matches[1];

								# Fall back to just using the root folder as the branch name
								} else if ( !$t_ignore_paths && preg_match( '/\/([^\/]+)/', $t_file->filename, $t_matches ) ) {
									$t_changeset->branch = $t_matches[1];
								}
							}
						}
					}
				}

			# Changeset comments
			} elseif ( 3 == $t_state ) {
				if ( $t_line == $t_svnline ) {
					$t_state = 1;
				} else {
					if ( !is_blank($t_changeset->message) ) {
						$t_changeset->message .= "\n$t_line";
					} else {
						$t_changeset->message .= $t_line;
					}
				}

			# Should only happen at the end...
			} else {
				break;
			}
		}

		if ( !is_null( $t_changeset ) && !is_blank( $t_changeset->branch ) ) {
			$t_changeset->save();
			$t_changesets[] = $t_changeset;
		}

		return $t_changesets;
	}
}
