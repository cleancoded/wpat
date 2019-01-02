<?php 

/*****************************************************************/
/* SYSTEM INFO */
/*****************************************************************/

class WPAT_Sys_info {

    private $_db;
    private static $_instance;

    public function __construct() {
        global $wpdb;
        $this->_db = $wpdb;  
    }
    
    public static function getInstance() {
        if( ! self::$_instance ) {
            self::$_instance = new WPAT_Sys_info();
        }
        return self::$_instance;
    }

    function memory_size_convert($size) {
        $l = substr($size, -1);
        $ret = substr($size, 0, -1);
        switch (strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }
        return $ret;
    }
    
    // Server and WP PHP Memory Limit
    function getServerWPMemoryLimit() { 
        if( @ini_get( 'memory_limit' ) == '-1' ) {
            $memory_limit = '-1 / ' . esc_html__( 'Unlimited', 'wpat' ) . ' (' . (int)WP_MEMORY_LIMIT . ' MB)';
        } else {
            $memory_limit = (int)@ini_get( 'memory_limit' ) . ' MB' . ' (' . (int)WP_MEMORY_LIMIT . ' MB)';
        } 

        if( (int)WP_MEMORY_LIMIT < (int)@ini_get('memory_limit') && WP_MEMORY_LIMIT != '-1' || (int)WP_MEMORY_LIMIT < (int)@ini_get('memory_limit') && @ini_get('memory_limit') != '-1' ) {
            $memory_limit .= ' <span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( 'The WP PHP Memory Limit is less than the %s Server PHP Memory Limit', 'wpat' ), (int)@ini_get('memory_limit') . ' MB' ) . '!</span>';
        }

        return $memory_limit;
    }

    // PHP Version
    function getPhpVersion() {        
        if( function_exists('phpversion') ) {
            $php_version = phpversion();
            if( version_compare( $php_version, '7.2', '<' ) ) {
                $php_version = '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - Recommend  PHP version of 7.2. See: %s', 'wpat'), esc_html( $php_version ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . __('WordPress Requirements', 'wpat') . '</a>') . '</span>';
            } else {
                $php_version = esc_html( $php_version );
            }
        } else {
            if( PHP_VERSION === false ) {
                $php_version = esc_html__( 'N/A', 'wpat' );
            } else {
                $php_version = PHP_VERSION;
            }
            
        }
        
        return $php_version;
    }

    // PHP Version Lite
    function getPhpVersionLite() {        
        if( function_exists('phpversion') ) {
            $php_version = phpversion();
        } else {
            if( PHP_VERSION === false ) {
                $php_version = esc_html__( 'N/A', 'wpat' );
            } else {
                $php_version = PHP_VERSION;
            }
            
        }
        
        return $php_version;
    }

    // cURL Version
    function getcURLVersion() {
        if( function_exists( 'curl_version' ) ) {
            $curl_version = curl_version();
            $curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
        } else {
            $curl_version = esc_html__( 'N/A', 'wpat' );
        }
        
        return $curl_version;        
    }

    // MySQL Version
    function getMySQLVersion() {
        if( $this->_db->use_mysqli ) {
            $ver = mysqli_get_server_info( $this->_db->dbh );
        } else {
            $ver = mysql_get_server_info();
        }
        if( ! empty( $this->_db->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) {
            $get_version = $this->_db->db_version();
            if( version_compare( $get_version, '5.6', '<') ) {
                $mysql_version = '<span class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - We recommend a minimum MySQL version of 5.6. See: %s', 'wpat'), esc_html( $get_version ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . __('WordPress Requirements', 'wpat') . '</a>') . '</span>';
            } else {
                $mysql_version = esc_html( $get_version );
            }
        } else {
            $mysql_version = esc_html__( 'N/A', 'wpat' );
        }
        
        return $mysql_version;
    }

    // MySQL Version Lite
    function getMySQLVersionLite() {
        if( $this->_db->use_mysqli ) {
            $ver = mysqli_get_server_info( $this->_db->dbh );
        } else {
            $ver = mysql_get_server_info();
        }
        if( ! empty( $this->_db->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) {
            $get_version = $this->_db->db_version();
            $mysql_version = esc_html( $get_version );
        } else {
            $mysql_version = esc_html__( 'N/A', 'wpat' );
        }
        
        return $mysql_version;
    }
    
    // Table Prefix   
    function get_table_prefix() {        
        global $wpdb;    
        
        $table_prefix = array(
            'tablePrefix' => $wpdb->prefix,
            'tableBasePrefix' => $wpdb->base_prefix,
        );
        
        return $table_prefix;    
    }
    
    // Shell Enabled    
    function isShellEnabled() {
        // Check if shell_exec() is enabled on this server
        if( function_exists('shell_exec') && ! in_array( 'shell_exec', array_map( 'trim', explode( ', ', ini_get( 'disable_functions' ) ) ) ) ) {
            // If enabled, check if shell_exec() actually have execution power
            $returnVal = shell_exec( 'cat /proc/cpuinfo' );
            if( ! empty( $returnVal ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    // Server Uptime
    function getServerUptime() {       
        $str = @file_get_contents('/proc/uptime');
        $num = floatval($str);
        $secs = fmod($num, 60); 
        $num = (int)($num / 60);
        $mins = $num % 60;      
        $num = (int)($num / 60);
        $hours = $num % 24;      
        $num = (int)($num / 24);
        $days = $num;
        
        $uptime = $days . ' ' . esc_html__( 'Days', 'wpat' ) . ' ' . $hours . ' ' . esc_html__( 'Hours', 'wpat' ) . ' ' . $mins . ' ' . esc_html__( 'Minutes', 'wpat' );
        
        return $uptime;
    }
    
    // WP Timezone
    function wp_timezone() {        
		$timezone = get_option('timezone_string'); // Direct value

		// Create a UTC+- zone if no timezone string exists
		if( empty( $timezone ) ) {
			// Current offset
			$current_offset = get_option('gmt_offset');

			// No offset
			if( 0 == $current_offset ) {
				$timezone = 'UTC+0';
			// Negative offset
			} elseif( $current_offset < 0 ) {
				$timezone = 'UTC' . $current_offset;
			// Plus offset
			} else {
				$timezone = 'UTC+' . $current_offset;
			}

			// Normalize
			$timezone = str_replace( array('.25','.5','.75'), array(':15',':30',':45'), $timezone );
		}

		return $timezone;
	}
    
    // Total Server RAM
    public function getServerRamTotal() {
        
        $result = 0;
        //$result = get_transient( 'get_server_ram_total' ); // get result from wp transient 
        
        // Linux server
        if( PHP_OS == 'Linux' ) {
            //if( $result === FALSE ) {
                $fh = fopen( '/proc/meminfo', 'r' );
                while( $line = fgets( $fh ) ) {
                    $pieces = array();
                    if( preg_match( '/^MemTotal:\s+(\d+)\skB$/', $line, $pieces ) ) {
                        $result = $pieces[1];
                        // KB to Bytes
                        $result = round( $result / 1024 / 1024, 2 );
                        break;
                    }
                }
                fclose( $fh );
                //$result = set_transient( 'get_server_ram_total', $result, WEEK_IN_SECONDS ); // store the result
            //}            
        } else {
            $result = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        // KB RAM Total
        return $result;
    }
    
    // Free Server RAM
    public function getServerRamFree() {
        $result = 0;
        // Linux server
        if( PHP_OS == 'Linux' ) {
            $fh = fopen( '/proc/meminfo', 'r' );
            while( $line = fgets( $fh ) ) {
                $pieces = array();
                if( preg_match( '/^MemFree:\s+(\d+)\skB$/', $line, $pieces ) ) {
                    // KB to Bytes
                    $result = round($pieces[1] / 1024 / 1024,2);
                    break;
                }
            }
            fclose( $fh );
        } else {
            $result = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        // KB RAM Total
        return $result;
    }
    
    // Detail Server RAM Info
    function getServerRamDetail() {
        $ram_data = '';
        if( PHP_OS == 'Linux' ) {
            foreach( file( '/proc/meminfo' ) as $ri ) {
                $m[strtok( $ri, ':') ] = strtok('');
            }

            //print("<pre>".print_r(file('/proc/meminfo'),true)."</pre>");
            
            $ram_total = round( (int)$m['MemTotal'] / 1024 / 1024, 2 );
            $ram_available = round( (int)$m['MemAvailable'] / 1024 / 1024, 2 );
            $ram_free = round( (int)$m['MemFree'] / 1024 / 1024, 2 );
            $ram_buffers = round( (int)$m['Buffers'] / 1024 / 1024, 2 );
            $ram_cached = round( (int)$m['Cached'] / 1024 / 1024, 2 );
            
            $mem_kernel_app = round( ( 100 - ( $ram_buffers + $ram_cached + $ram_free ) / $ram_total * 100 ), 2 );
            $mem_cached = round( $ram_cached / $ram_total * 100, 2 );
            $mem_buffers = round( $ram_buffers / $ram_total * 100, 2 );
            
            $ram_data = array(
                'MemTotal' => $ram_total,
                'MemAvailable' => $ram_available,
                'MemFree' => $ram_free,
                'Buffers' => $ram_buffers,
                'Cached' => $ram_cached,
                'MemUsagePercentage' => round( $mem_kernel_app + $mem_buffers + $mem_cached, 2 ), // Physical Memory
            );
            
        } else {
            $ram_data = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        return $ram_data;        
    }
    
    // WP Memory Usage (relative to the defined WP_MEMORY_LIMIT)  
    function getRealMemoryUsage() {
        $real_memory_usage = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage(true) ) : 0;
        return $real_memory_usage;
    }
    
    // WP Memory Usage (relative to the defined WP_MEMORY_LIMIT)  
    function wp_memory_usage() {
        
        // Get WP Memory Limit
        if( (int)WP_MEMORY_LIMIT > (int)@ini_get( 'memory_limit' ) ) {   
            // WP Limit can't be greater than Server Limiit
            $get_memory_limit = @ini_get( 'memory_limit' );
        } else {
            $get_memory_limit = WP_MEMORY_LIMIT;
        }
        
        $memory_limit_convert = WPAT_Sys_info::memory_size_convert( $get_memory_limit );
        $memory_limit_format = size_format( $memory_limit_convert );
        $memory_limit = $memory_limit_convert;
        
        // Get Real Memory Usage
        $get_memory_usage = WPAT_Sys_info::getRealMemoryUsage();
        $memory_usage_convert = round( $get_memory_usage / 1024 / 1024 );
        $memory_usage_format = $memory_usage_convert . ' MB';
        $memory_usage = $get_memory_usage;

        if( $get_memory_usage != false && $get_memory_limit != false ) {
            
            // check memory limit is a numeric value
            if( ! is_numeric( $memory_limit ) ) $memory_limit = 999;
            
            $wp_mem_data = array(
                'MemLimit' => $memory_limit,
                'MemLimitGet' => $get_memory_limit,
                'MemLimitConvert' => $memory_limit_convert,
                'MemLimitFormat' => $memory_limit_format,
                'MemUsage' => $memory_usage,
                'MemUsageGet' => $get_memory_usage,
                'MemUsageConvert' => $memory_usage_convert,
                'MemUsageFormat' => $memory_usage_format,
                'MemUsageCalc' => round( $memory_usage / $memory_limit * 100, 0 ),
            );
        } else {
            $wp_mem_data = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        return $wp_mem_data;
    }
    
    // Server Memory Usage  
    function server_memory_usage() {
        
        // Get Server Memory Limit
        $get_memory_limit = @ini_get( 'memory_limit' );
        $memory_limit_convert = WPAT_Sys_info::memory_size_convert( $get_memory_limit );
        $memory_limit_format = size_format( $memory_limit_convert );
        $memory_limit = $memory_limit_convert;
        
        // Get Real Memory Usage
        $get_memory_usage = WPAT_Sys_info::getRealMemoryUsage();
        $memory_usage_convert = round( $get_memory_usage / 1024 / 1024 );
        $memory_usage_format = $memory_usage_convert . ' MB';
        $memory_usage = $get_memory_usage;        
        
        if( $get_memory_usage != false && $get_memory_limit != false ) {
            
            // check memory limit is a numeric value
            if( ! is_numeric( $memory_limit ) ) $memory_limit = 999;
            
            $php_mem_data = array(
                'MemLimit' => $memory_limit,
                'MemLimitGet' => $get_memory_limit,
                'MemLimitConvert' => $memory_limit_convert,
                'MemLimitFormat' => $memory_limit_format,
                'MemUsage' => $memory_usage,
                'MemUsageGet' => $get_memory_usage,
                'MemUsageConvert' => $memory_usage_convert,
                'MemUsageFormat' => $memory_usage_format,
                'MemUsageCalc' => round( $memory_usage / $memory_limit * 100, 0 ),
            );
        } else {
            $php_mem_data = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        return $php_mem_data;
    }
    
    // Server Harddisk Infos    
    public function getServerDiskSize( $path = '/' ) {
        $result = array();
        $result['size'] = 0;
        $result['free'] = 0;
        $result['used'] = 0;
        // Linux server
        if( PHP_OS == 'Linux' ) {
            $lines = null;
            exec( sprintf( 'df /P %s', $path ), $lines );
            foreach( $lines as $index => $line ) {
                if( $index != 1 ) {
                    continue;
                }
                $values = preg_split( '/\s{1,}/', $line );
                $result['size'] = round( $values[1] / 1024 / 1024, 2 );
                $result['free'] = round( $values[3] / 1024 / 1024, 2 );
                $result['used'] = round( $values[2] / 1024 / 1024, 2 );
                $result['usage'] = round( $result['used'] / $result['size'] * 100, 2 );
                break;
            }
        } else {
            $result = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        return $result;
    }
        
    // Server CPU count
    function check_cpu_count() {
        $cpu_count = get_transient( 'wpss_cpu_count' );

        if( $cpu_count === FALSE ) {
            if( $this->isShellEnabled() ) {
                $cpu_count = shell_exec('cat /proc/cpuinfo |grep "physical id" | sort | uniq | wc -l');
                set_transient( 'wpss_cpu_count', $cpu_count, WEEK_IN_SECONDS );
            } else {
                $cpu_count = esc_html__( 'N/A', 'wpat' ) . '.';
            }
        }

        return $cpu_count;
    }
    
    // Server CPU LOAD AVERAGE
    function cpu_load_average() {		
		$load = esc_html__( 'N/A', 'wpat' ) . '.';

		// Check via PHP function
		$avg = function_exists('sys_getloadavg')? sys_getloadavg() : false;
		if (!empty($avg) && is_array($avg) && 3 == count($avg))
			$load = implode(', ', $avg);
        
		return $load;
	}
    
    // Server CPU Core count
    function check_core_count() {
        $cmd = "uname";
        $OS = strtolower( trim( shell_exec( $cmd ) ) );
 
        switch( $OS ) {
           case('linux'):
              $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
              break;
           case('freebsd'):
              $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
              break;
           default:
              unset( $cmd );
        }
 
        if( $cmd != '' ){
           $cpuCoreNo = intval( trim( shell_exec( $cmd ) ) );
        }
        
        return empty( $cpuCoreNo ) ? 1 : $cpuCoreNo;        
    }
    
    // Server CPU Load Percentage
    public function getServerCpuLoadPercentage() {
        $result = -1;
        $lines = null;
        // Linux server
        if( PHP_OS == 'Linux' ) {
            $checks = array();
            foreach( array( 0, 1 ) as $i ) {
                $cmd = '/proc/stat';
                $lines = array();
                $fh = fopen( $cmd, 'r' );
                while( $line = fgets( $fh ) ) {
                    $lines[] = $line;
                }
                fclose( $fh );
                foreach( $lines as $line ) {
                    $ma = array();
                    if( ! preg_match( '/^cpu  (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+)$/', $line, $ma ) ) {
                        continue;
                    }
                    $total = $ma[1] + $ma[2] + $ma[3] + $ma[4] + $ma[5] + $ma[6] + $ma[7] + $ma[8] + $ma[9];
                    //$totalCpu = $ma[1] + $ma[2] + $ma[3];
                    //$result = (100 / $total) * $totalCpu;
                    $ma['total'] = $total;
                    $checks[] = $ma;
                    break;
                }
                if( $i == 0 ) {
                    // Wait before checking again.
                    sleep(1);
                }
            }
            // Idle - prev idle
            $diffIdle = $checks[1][4] - $checks[0][4];
            // Total - prev total
            $diffTotal = $checks[1]['total'] - $checks[0]['total'];
            // Usage in %
            $diffUsage = round( ( 1000 * ( $diffTotal - $diffIdle ) / $diffTotal + 5 ) / 10, 2 );
            $result = $diffUsage;
        } else {
            $result = esc_html__( 'N/A', 'wpat' ) . '.';
        }
        
        return (float) $result;
    }

}


/*****************************************************************/
/* SYSTEM INFO ADMIN PAGE */
/*****************************************************************/

if ( ! function_exists( 'wpat_sys_info_admin_menu' ) ) :

	function wpat_sys_info_admin_menu() {
			
		add_submenu_page(
			'tools.php',
			esc_html__( 'WPAT - System Info', 'wpat' ),
			esc_html__( 'WPAT System Info', 'wpat' ),
			'manage_options',
			'wpat_sys_info',
			'wpat_sys_info_page'
		);
		
	}

	add_action( 'admin_menu', 'wpat_sys_info_admin_menu' );

	function wpat_sys_info_page() { 
        global $wpdb; 
        $common = new WPAT_Sys_info(); 

        $help = '<span class="dashicons dashicons-editor-help"></span>';
        $enabled = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Enabled', 'wpat' ) . '</span>';
        $disabled = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Disabled', 'wpat' ) . '</span>';
        $yes = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Yes', 'wpat' ) . '</span>';
        $no = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'No', 'wpat' ) . '</span>';
        $entered = '<span class="sys-status enable"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Defined', 'wpat' ) . '</span>';
        $not_entered = '<span class="sys-status disable"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Not defined', 'wpat' ) . '</span>';
        $sec_key = '<span class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Please enter this security key in the wp-confiq.php file', 'wpat' ) . '!</span>'; ?>
	
        <div class="wrap">
            <h1><?php echo esc_html__( 'WPAT - System Info', 'wpat' ); ?></h1>

            <h2><?php echo esc_html__( 'WordPress Information', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'First, you can see the most important information about your WordPress installation at a glance. Learn more about the <a href="https://wordpress.org/about/requirements/" target="_blank">requirements</a>', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php esc_html_e( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php esc_html_e( 'Result', 'wpat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="25%"><?php esc_html_e( 'WP Version', 'wpat' ); ?>:</td>
                        <td><strong><?php bloginfo('version'); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('PHP Version', 'wpat'); ?>:</td>
                        <td><?php echo $common->getPhpVersion(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'MySQL Version', 'wpat' ); ?>:</td>
                        <td><?php echo $common->getMySQLVersion(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory WP-Limit', 'wpat' ); ?>:</td>
                        <td><?php
                            $memory = $common->memory_size_convert( WP_MEMORY_LIMIT );

                            if ($memory < 67108864) {
                                echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', 'wpat'), size_format($memory), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __('Increasing memory allocated to PHP', 'wpat') . '</a>') . '</span>';
                            } else {
                                echo '<strong>' . size_format($memory) . '</strong>';
                            }
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory Server-Limit', 'wpat' ); ?>:</td>
                        <td>
                            <?php
                            if (function_exists('memory_get_usage')) {
                                $system_memory = $common->memory_size_convert(@ini_get('memory_limit'));
                                $memory = max($memory, $system_memory);
                            }

                            if ($memory < 67108864) {
                                echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', 'wpat'), size_format($memory), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __('Increasing memory allocated to PHP', 'wpat') . '</a>') . '</span>';
                            } else {
                                echo '<strong>' . size_format($memory) . '</strong>';
                            }
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory WP-Usage', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $common->wp_memory_usage()['MemLimitGet'] == '-1' ) { ?>
                                <?php echo $common->wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . esc_html__( 'Unlimited', 'wpat' ) . ' (-1)'; ?>
                            <?php } else { ?>
                                <div class="status-progressbar"><span><?php echo $common->wp_memory_usage()['MemUsageCalc'] . '% '; ?></span><div style="width: <?php echo $common->wp_memory_usage()['MemUsageCalc']; ?>%"></div></div>
                                <?php echo $common->wp_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . $common->wp_memory_usage()['MemLimitFormat']; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Memory Server-Usage', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( $common->server_memory_usage()['MemLimitGet'] == '-1' ) { ?>
                                <?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . esc_html__( 'Unlimited', 'wpat' ) . ' (-1)'; ?>
                            <?php } else { ?>
                                <div class="status-progressbar"><span><?php echo $common->server_memory_usage()['MemUsageCalc'] . '% '; ?></span><div style="width: <?php echo $common->server_memory_usage()['MemUsageCalc']; ?>%"></div></div>
                                <?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . $common->server_memory_usage()['MemLimitFormat']; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'PHP Max Upload Size (WP)', 'wpat' ); ?>:</td>
                        <td><?php echo (int)ini_get('upload_max_filesize') . ' MB (' . size_format( wp_max_upload_size() ) . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Home URL', 'wpat'); ?>:</td>
                        <td><?php echo get_home_url(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Site URL', 'wpat'); ?>:</td>
                        <td><?php echo get_site_url(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('Document Root', 'wpat'); ?>:</td>
                        <td><?php echo get_home_path(); ?></td>
                    </tr>
                </tbody>
            </table>
            
            
            <h2><?php echo esc_html__( 'WordPress Contstants Overview', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'Use the following contstants to manage important settings of your WordPress installation in the <code>wp-config.php</code> file. Learn more about <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">here</a>', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php esc_html_e( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php esc_html_e( 'Result', 'wpat' ); ?></th>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Example', 'wpat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'WP Language', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Language_and_Language_Directory" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WPLANG') && WPLANG ) : 
                                echo WPLANG;
                            else :
                                echo $not_entered . ' / ' . get_locale();
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WPLANG', 'de_DE' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Force SSL Admin', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Require_SSL_for_Admin_and_Logins" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FORCE_SSL_ADMIN') && true === FORCE_SSL_ADMIN ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FORCE_SSL_ADMIN', true );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP PHP Memory Limit', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( WP_MEMORY_LIMIT == '-1' ) {
                                echo '-1 / ' . esc_html__( 'Unlimited', 'wpat' );
                            } else {
                                echo (int)WP_MEMORY_LIMIT . ' MB';                                                              
                            } 
                            echo ' (' . esc_html__( 'defined limit', 'wpat' ) . ')'; 
        
                            if( (int)WP_MEMORY_LIMIT < (int)ini_get('memory_limit') && WP_MEMORY_LIMIT != '-1' ) {
                                echo ' <span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( 'The WP PHP Memory Limit is less than the %s Server PHP Memory Limit', 'wpat' ), (int)ini_get('memory_limit') . ' MB' ) . '!</span>';
                            } ?>
                        </td>
                        <td><?php echo "define( 'WP_MEMORY_LIMIT', '64M' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP PHP Max Memory Limit', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( WP_MAX_MEMORY_LIMIT == '-1' ) {
                                echo '-1 / ' . esc_html__( 'Unlimited', 'wpat' );
                            } else {
                                echo (int)WP_MAX_MEMORY_LIMIT . ' MB';
                            } 
                            echo ' (' . esc_html__( 'defined limit', 'wpat' ) . ')'; ?>
                        </td>
                        <td><?php echo "define( 'WP_MAX_MEMORY_LIMIT', '256M' );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Post Revisions', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Post_Revisions" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_POST_REVISIONS') && WP_POST_REVISIONS == false ) {
                                esc_html_e( 'Disabled', 'wpat' );
                            } else {
                                echo WP_POST_REVISIONS;
                            } ?>
                        </td>
                        <td><?php echo "define( 'WP_POST_REVISIONS', false );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Autosave Interval', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Modify_AutoSave_Interval" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('AUTOSAVE_INTERVAL') && AUTOSAVE_INTERVAL ) : 
                                echo AUTOSAVE_INTERVAL . ' ' . esc_html__( 'Seconds', 'wpat' );
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'AUTOSAVE_INTERVAL', 160 );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Mail Interval', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('WP_MAIL_INTERVAL') && WP_MAIL_INTERVAL ) : 
                                echo WP_MAIL_INTERVAL . ' ' . esc_html__( 'Seconds', 'wpat' );
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_MAIL_INTERVAL', 60 );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Empty Trash', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Empty_Trash" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( EMPTY_TRASH_DAYS == 0 ) {
                                echo $disabled;
                            } else {
                                echo EMPTY_TRASH_DAYS . ' ' . 'Days';
                            } ?>
                        </td>
                        <td><?php echo "define( 'EMPTY_TRASH_DAYS', 30 );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Media Trash', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('MEDIA_TRASH') && true === MEDIA_TRASH ) :
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'MEDIA_TRASH', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Cleanup Image Edits', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Cleanup_Image_Edits" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('IMAGE_EDIT_OVERWRITE') && true === IMAGE_EDIT_OVERWRITE ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'IMAGE_EDIT_OVERWRITE', true );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Multisite', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Enable_Multisite_.2F_Network_Ability" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_ALLOW_MULTISITE') && true === WP_ALLOW_MULTISITE ) :
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_ALLOW_MULTISITE', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Main Site Domain', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('DOMAIN_CURRENT_SITE') && DOMAIN_CURRENT_SITE ) : 
                                echo DOMAIN_CURRENT_SITE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DOMAIN_CURRENT_SITE', 'www.domain.com' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Main Site Path', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('PATH_CURRENT_SITE') && PATH_CURRENT_SITE ) : 
                                echo PATH_CURRENT_SITE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'PATH_CURRENT_SITE', '/path/to/wordpress/' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Main Site ID', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('SITE_ID_CURRENT_SITE') && SITE_ID_CURRENT_SITE ) : 
                                echo SITE_ID_CURRENT_SITE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SITE_ID_CURRENT_SITE', 1 );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Main Site Blog ID', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('BLOG_ID_CURRENT_SITE') && BLOG_ID_CURRENT_SITE ) : 
                                echo BLOG_ID_CURRENT_SITE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'BLOG_ID_CURRENT_SITE', 1 );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Allow Subdomain Install', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('SUBDOMAIN_INSTALL') && true === SUBDOMAIN_INSTALL ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SUBDOMAIN_INSTALL', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Allow Subdirectory Install', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('ALLOW_SUBDIRECTORY_INSTALL') && true === ALLOW_SUBDIRECTORY_INSTALL ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ALLOW_SUBDIRECTORY_INSTALL', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Site Specific Upload Directory', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('BLOGUPLOADDIR') && BLOGUPLOADDIR ) : 
                                echo BLOGUPLOADDIR;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'BLOGUPLOADDIR', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Upload Base Directory', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('UPLOADBLOGSDIR') && UPLOADBLOGSDIR ) : 
                                echo UPLOADBLOGSDIR;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'UPLOADBLOGSDIR', 'wp-content/blogs.dir' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Load Sunrise', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('SUNRISE') && true === SUNRISE ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SUNRISE', true );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Debug Mode', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Debug" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_DEBUG') && WP_DEBUG ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_DEBUG', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Debug Log', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Logging" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_DEBUG_LOG', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Debug Display', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Logging" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_DEBUG_DISPLAY', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Script Debug', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Debug" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SCRIPT_DEBUG', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Save Queries', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Save_queries_for_analysis" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('SAVEQUERIES') && SAVEQUERIES ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SAVEQUERIES', true );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Automatic Updates', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_WordPress_Auto_Updates" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED ) : 
                                echo $disabled;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'AUTOMATIC_UPDATER_DISABLED', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Core Updates', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_WordPress_Core_Updates" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_AUTO_UPDATE_CORE') && false === WP_AUTO_UPDATE_CORE ) : 
                                echo $disabled;
                            elseif( defined('WP_AUTO_UPDATE_CORE') && 'minor' === WP_AUTO_UPDATE_CORE ) : 
                                echo $enabled . ' / <span class="error"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Only for minor updates', 'wpat' ) . '</span>';
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_AUTO_UPDATE_CORE', false );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Default Theme Updates', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('CORE_UPGRADE_SKIP_NEW_BUNDLED') && true === CORE_UPGRADE_SKIP_NEW_BUNDLED ) : 
                                echo $disabled;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Plugin and Theme Editor', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_the_Plugin_and_Theme_Editor" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DISALLOW_FILE_EDIT') && true === DISALLOW_FILE_EDIT ) : 
                                echo $disabled;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DISALLOW_FILE_EDIT', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Plugin and Theme Updates', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_Plugin_and_Theme_Update_and_Installation" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DISALLOW_FILE_MODS') && true === DISALLOW_FILE_MODS ) : 
                                echo $disabled;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DISALLOW_FILE_MODS', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Default Theme', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('WP_DEFAULT_THEME') && WP_DEFAULT_THEME ) : 
                                echo WP_DEFAULT_THEME;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_DEFAULT_THEME', 'default-theme-folder-name' );" ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Alternate Cron', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Alternative_Cron" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('ALTERNATE_WP_CRON') && true === ALTERNATE_WP_CRON ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ALTERNATE_WP_CRON', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Cron', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_Cron_and_Cron_Timeout" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ) : 
                                echo $disabled;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DISABLE_WP_CRON', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Cron Lock Timeout', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Alternative_Cron" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_CRON_LOCK_TIMEOUT') && WP_CRON_LOCK_TIMEOUT ) : 
                                echo WP_CRON_LOCK_TIMEOUT . ' ' . esc_html__( 'Seconds', 'wpat' );
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_CRON_LOCK_TIMEOUT', 60 );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e('WP Cache', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Cache" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_CACHE') && true === WP_CACHE ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_CACHE', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Concatenate Admin JS/CSS', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_Javascript_Concatenation" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('CONCATENATE_SCRIPTS') && false === CONCATENATE_SCRIPTS || true === SCRIPT_DEBUG ) :
                                echo $disabled;
                                if( true === SCRIPT_DEBUG ) :
                                    echo ' / <span class="warning"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Not available if WP Script Debug is true', 'wpat' ) . '</span>';
                                endif;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'CONCATENATE_SCRIPTS', false );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Compress Admin JS', 'wpat'); ?>:</td>
                        <td>
                            <?php if( defined('COMPRESS_SCRIPTS') && false === COMPRESS_SCRIPTS || true === SCRIPT_DEBUG ) :
                                echo $disabled;
                                if( true === SCRIPT_DEBUG ) :
                                    echo ' / <span class="warning"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Not available if WP Script Debug is true', 'wpat' ) . '</span>';
                                endif;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'COMPRESS_SCRIPTS', false );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Compress Admin CSS', 'wpat'); ?>:</td>
                        <td>
                            <?php if( defined('COMPRESS_CSS') && false === COMPRESS_CSS || true === SCRIPT_DEBUG ) :
                                echo $disabled;
                                if( true === SCRIPT_DEBUG ) :
                                    echo ' / <span class="warning"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Not available if WP Script Debug is true', 'wpat' ) . '</span>';
                                endif;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'COMPRESS_CSS', false );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Enforce GZip Admin JS/CSS', 'wpat'); ?>:</td>
                        <td>
                            <?php if( ! defined('ENFORCE_GZIP') || defined('ENFORCE_GZIP') && false === ENFORCE_GZIP || true === SCRIPT_DEBUG ) :
                                echo $disabled;
                                if( true === SCRIPT_DEBUG ) :
                                    echo ' / <span class="warning"><span class="dashicons dashicons-warning"></span> ' . esc_html__( 'Not available if WP Script Debug is true', 'wpat' ) . '</span>';
                                endif;
                            else :
                                echo $enabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ENFORCE_GZIP', true );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Allow unfiltered HTML', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Disable_unfiltered_HTML_for_all_users" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DISALLOW_UNFILTERED_HTML') && true === DISALLOW_UNFILTERED_HTML ) : 
                                echo $disabled . ' ' . esc_html__( 'for all users', 'wpat' );
                            else :
                                echo $enabled . ' ' . esc_html__( 'for users with administrator or editor roles', 'wpat' );
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DISALLOW_UNFILTERED_HTML', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Allow unfiltered Uploads', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('ALLOW_UNFILTERED_UPLOADS') && true === ALLOW_UNFILTERED_UPLOADS ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ALLOW_UNFILTERED_UPLOADS', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Block External URL Requests', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Block_External_URL_Requests" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_HTTP_BLOCK_EXTERNAL') && true === WP_HTTP_BLOCK_EXTERNAL ) : 
                                echo $enabled;
                                if( defined('WP_ACCESSIBLE_HOSTS') ) :
                                    echo ' / ' . esc_html__( 'Accessible Hosts', 'wpat' ) . ': ' . WP_ACCESSIBLE_HOSTS;
                                endif; 
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_HTTP_BLOCK_EXTERNAL', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Redirect Nonexistent Blogs', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Redirect_Nonexistent_Blogs" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('NOBLOGREDIRECT') && NOBLOGREDIRECT != '' ) :
                                echo NOBLOGREDIRECT;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'NOBLOGREDIRECT', 'http://example.com' );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Cookie Domain', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Set_Cookie_Domain" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('COOKIE_DOMAIN') && COOKIE_DOMAIN != '' ) :
                                echo COOKIE_DOMAIN;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'COOKIE_DOMAIN', 'www.example.com' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Cookie Hash', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('COOKIEHASH') && COOKIEHASH ) :
                                echo COOKIEHASH;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'COOKIEHASH', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Auth Cookie', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('AUTH_COOKIE') && AUTH_COOKIE ) :
                                echo AUTH_COOKIE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'AUTH_COOKIE', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Secure Auth Cookie', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('SECURE_AUTH_COOKIE') && SECURE_AUTH_COOKIE ) :
                                echo SECURE_AUTH_COOKIE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SECURE_AUTH_COOKIE', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Cookie Path', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Additional_Defined_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('COOKIEPATH') && COOKIEPATH ) :
                                echo COOKIEPATH;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'COOKIEPATH', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Site Cookie Path', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Additional_Defined_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('SITECOOKIEPATH') && SITECOOKIEPATH ) :
                                echo SITECOOKIEPATH;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'SITECOOKIEPATH', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Admin Cookie Path', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Additional_Defined_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('ADMIN_COOKIE_PATH') && ADMIN_COOKIE_PATH ) :
                                echo ADMIN_COOKIE_PATH;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ADMIN_COOKIE_PATH', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Plugins Cookie Path', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Additional_Defined_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('PLUGINS_COOKIE_PATH') && PLUGINS_COOKIE_PATH ) :
                                echo PLUGINS_COOKIE_PATH;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'PLUGINS_COOKIE_PATH', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Logged In Cookie', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('LOGGED_IN_COOKIE') && LOGGED_IN_COOKIE ) :
                                echo LOGGED_IN_COOKIE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'LOGGED_IN_COOKIE', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Test Cookie', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('TEST_COOKIE') && TEST_COOKIE ) :
                                echo TEST_COOKIE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'TEST_COOKIE', '' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP User Cookie', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( defined('USER_COOKIE') && USER_COOKIE ) :
                                echo USER_COOKIE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'USER_COOKIE', '' );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Directory Permission', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Override_of_default_file_permissions" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FS_CHMOD_DIR') && FS_CHMOD_DIR ) : 
                                echo 'chmod' . ' ' . FS_CHMOD_DIR;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP File Permission', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Override_of_default_file_permissions" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FS_CHMOD_FILE') && FS_CHMOD_FILE ) : 
                                echo 'chmod' . ' ' . FS_CHMOD_FILE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );" ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP FTP Method', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FS_METHOD') && FS_METHOD ) : 
                                echo FS_METHOD;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FS_METHOD', 'ftpext' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Base', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_BASE') && FTP_BASE ) : 
                                echo FTP_BASE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_BASE', '/path/to/wordpress/' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Content Dir', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_CONTENT_DIR') && FTP_CONTENT_DIR ) : 
                                echo FTP_CONTENT_DIR;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_CONTENT_DIR', '/path/to/wordpress/wp-content/' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Plugin Dir', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_PLUGIN_DIR') && FTP_PLUGIN_DIR ) : 
                                echo FTP_PLUGIN_DIR;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_PLUGIN_DIR ', '/path/to/wordpress/wp-content/plugins/' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP SSH Public Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_PUBKEY') && FTP_PUBKEY ) : 
                                echo FTP_PUBKEY;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_PUBKEY', '/home/username/.ssh/id_rsa.pub' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP SSH Private Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_PRIKEY') && FTP_PRIKEY ) : 
                                echo FTP_PRIKEY;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_PRIKEY', '/home/username/.ssh/id_rsa' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Username', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_USER') && FTP_USER ) : 
                                echo FTP_USER;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_USER', 'username' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Password', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_PASS') && FTP_PASS ) : 
                                echo '****';
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_PASS', 'password' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP Host', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_HOST') && FTP_HOST ) : 
                                echo FTP_HOST;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_HOST', 'ftp.example.org' );" ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP FTP SSL', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('FTP_SSL') && true === FTP_SSL ) : 
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'FTP_SSL', false );" ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Site URL', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WP_SITEURL" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_SITEURL') && WP_SITEURL ) : 
                                echo WP_SITEURL;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_SITEURL', 'http://example.com/wordpress' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Home', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#WP_HOME" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_HOME') && WP_HOME ) : 
                                echo WP_HOME;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_HOME', 'http://example.com' );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'WP Uploads Path', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_uploads_folder" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('UPLOADS') && '' != UPLOADS ) : 
                                echo UPLOADS;
                            else :
                                $upload_dir = wp_upload_dir();
                                echo $upload_dir['basedir'];
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'UPLOADS', dirname(__FILE__) . 'wp-content/media' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Template Path', 'wpat'); ?>:</td>
                        <td>
                            <?php echo TEMPLATEPATH; ?>
                        </td>
                        <td><?php echo "define( 'TEMPLATEPATH', dirname(__FILE__) . 'wp-content/themes/theme-folder' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Stylesheet Path', 'wpat'); ?>:</td>
                        <td>
                            <?php echo STYLESHEETPATH; ?>
                        </td>
                        <td><?php echo "define( 'STYLESHEETPATH', dirname(__FILE__) . 'wp-content/themes/theme-folder' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Content Path', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo WP_CONTENT_DIR; ?>
                        </td>
                        <td><?php echo "define( 'WP_CONTENT_DIR', dirname(__FILE__) . '/blog/wp-content' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Content URL', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_wp-content_folder" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo WP_CONTENT_URL; ?>
                        </td>
                        <td><?php echo "define( 'WP_CONTENT_URL', 'http://example/blog/wp-content' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Plugin Path', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_plugin_folder" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo WP_PLUGIN_DIR; ?>
                        </td>
                        <td><?php echo "define( 'WP_PLUGIN_DIR', dirname(__FILE__) . '/blog/wp-content/plugins' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Plugin URL', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Moving_plugin_folder" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo WP_PLUGIN_URL; ?>
                        </td>
                        <td><?php echo "define( 'WP_PLUGIN_URL', 'http://example/blog/wp-content/plugins' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Language Path', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Language_and_Language_Directory" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo WP_LANG_DIR; ?>
                        </td>
                        <td><?php echo "define( 'WP_LANG_DIR', dirname(__FILE__) . '/wordpress/languages' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Temporary Files Path', 'wpat'); ?>:</td>
                        <td>
                            <?php if( defined('WP_TEMP_DIR') && '' != WP_TEMP_DIR ) : 
                                echo WP_TEMP_DIR;
                            else :
                                echo get_temp_dir();
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_TEMP_DIR', dirname(__FILE__) . 'wp-content/temp' );"; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <h2><?php echo esc_html__( 'Database Information', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'Use the following contstants to manage important database settings of your WordPress installation in the <code>wp-config.php</code> file. Learn more about <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">here</a>', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php echo esc_html__( 'Result', 'wpat' ); ?></th>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Example', 'wpat' ); ?></th>
                    </tr>
                </thead>  
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'MySQL Version', 'wpat' ); ?>:</td>
                        <td colspan="2"><?php echo $common->getMySQLVersion(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB Name', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Set_Database_Name" target="_blank"><?php echo $help; ?></a></td>
                        <td><?php echo DB_NAME; ?></td>
                        <td><?php echo "define( 'DB_NAME', 'MyDatabaseName' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB User', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Set_Database_User" target="_blank"><?php echo $help; ?></a></td>
                        <td><?php echo DB_USER; ?></td>
                        <td><?php echo "define( 'DB_USER', 'MyUserName' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB Host', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Set_Database_Host" target="_blank"><?php echo $help; ?></a></td>
                        <td><?php echo DB_HOST; ?></td>
                        <td><?php echo "define( 'DB_HOST', 'MyDatabaseHost' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB Password', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Set_Database_Password" target="_blank"><?php echo $help; ?></a></td>
                        <td><?php echo '***'; ?></td>
                        <td><?php echo "define( 'DB_PASSWORD', 'MyPassWord' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB Charset', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Database_character_set" target="_blank"><?php echo $help; ?></a></td>
                        <td><?php echo DB_CHARSET; ?></td>
                        <td><?php echo "define( 'DB_CHARSET', 'utf8' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'DB Collate', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Database_collation" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DB_COLLATE') && empty( DB_COLLATE ) ) {
                                echo $not_entered;
                            } else {
                                echo DB_COLLATE;
                            } ?>
                        </td>
                        <td><?php echo "define( 'DB_COLLATE', 'utf8_general_ci' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Allow DB Repair', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Automatic_Database_Optimizing" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('WP_ALLOW_REPAIR') && WP_ALLOW_REPAIR ) : 
                                echo v;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'WP_ALLOW_REPAIR', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Disallow Upgrade Global Tables', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Do_not_upgrade_global_tables" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('DO_NOT_UPGRADE_GLOBAL_TABLES') && true === DO_NOT_UPGRADE_GLOBAL_TABLES ) :
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DO_NOT_UPGRADE_GLOBAL_TABLES', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Custom User Table', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Custom_User_and_Usermeta_Tables" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('CUSTOM_USER_TABLE') && CUSTOM_USER_TABLE ) :
                                echo CUSTOM_USER_TABLE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'CUSTOM_USER_TABLE', &dollar;table_prefix.'my_users' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Custom User Meta Table', 'wpat'); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Custom_User_and_Usermeta_Tables" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( defined('CUSTOM_USER_META_TABLE') && CUSTOM_USER_META_TABLE ) :
                                echo CUSTOM_USER_META_TABLE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'CUSTOM_USER_META_TABLE', &dollar;table_prefix.'my_usermeta' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Display Database Errors', 'wpat'); ?>:</td>
                        <td>
                            <?php if( defined('DIEONDBERROR') && true === DIEONDBERROR ) :
                                echo $enabled;
                            else :
                                echo $disabled;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'DIEONDBERROR', true );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e('WP Database Error Log File', 'wpat'); ?>:</td>
                        <td>
                            <?php if( defined('ERRORLOGFILE') && ERRORLOGFILE ) :
                                echo ERRORLOGFILE;
                            else :
                                echo $not_entered;
                            endif; ?>
                        </td>
                        <td><?php echo "define( 'ERRORLOGFILE', '/absolute-path-to-file/' );"; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'Table Prefix', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#table_prefix" target="_blank"><?php echo $help; ?></a></td>
                        <td colspan="2"><?php echo $common->get_table_prefix()['tablePrefix']; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Table Base Prefix', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#table_prefix" target="_blank"><?php echo $help; ?></a></td>
                        <td colspan="2"><?php echo $common->get_table_prefix()['tableBasePrefix'] . ' (' . esc_html__( 'defined', 'wpat' ) . ')'; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <h2><?php echo esc_html__( 'Security Keys', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'Use the following contstants to set the security keys for your WordPress installation in the <code>wp-config.php</code> file. Learn more about <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">here</a>', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php echo esc_html__( 'Result', 'wpat' ); ?></th>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Example', 'wpat' ); ?></th>
                    </tr>
                </thead>  
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'WP Auth Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( AUTH_KEY ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'AUTH_KEY', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Secure Auth Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( SECURE_AUTH_KEY ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'SECURE_AUTH_KEY', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Logged In Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( LOGGED_IN_KEY ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'LOGGED_IN_KEY', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Nonce Key', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( NONCE_KEY ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'NONCE_KEY', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Auth Salt', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( AUTH_SALT ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'AUTH_SALT', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Secure Auth Salt', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( SECURE_AUTH_SALT ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'SECURE_AUTH_SALT', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Logged In Auth Salt', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( LOGGED_IN_SALT ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;
                            } ?>
                        </td>
                        <td><?php echo "define( 'LOGGED_IN_SALT', 'MyKey' );"; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'WP Nonce Salt', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php if( empty( NONCE_SALT ) ) {
                                echo $sec_key;
                            } else {
                                echo $entered;;
                            } ?>
                        </td>
                        <td><?php echo "define( 'NONCE_SALT', 'MyKey' );"; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <h2><?php echo esc_html__( 'Server Information', 'wpat' ); ?></h2>
            
            <p><?php echo __( 'Interesting information about your web server. You can also use <a href="http://linfo.sourceforge.net/" target="_blank">linfo</a> or <a href="https://phpsysinfo.github.io/phpsysinfo/" target="_blank">phpsysinfo</a> to get more information about the web server', 'wpat' ); ?>.</p>
            
            <p><?php echo __( 'In the most cases you can modify some server settings like "PHP Memory Limit" or "PHP Post Max Size" by upload and modify a <code>php.ini</code> file in the WordPress <code>/wp-admin/</code> folder. Learn more about <a href="https://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/" target="_blank">here</a>', 'wpat' ); ?>.</p>
            
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php echo esc_html__( 'Result', 'wpat' ); ?></th>
                    </tr>
                </thead>  
                <tbody>
                    <tr>
                        <td><?php esc_html_e( 'OS', 'wpat' ); ?>:</td>
                        <td><?php echo PHP_OS; ?> / <?php echo ( PHP_INT_SIZE * 8 ) . __('Bit OS', 'wpat') . ' (' . php_uname() . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Software', 'wpat' ); ?>:</td>
                        <td><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'IP Address', 'wpat' ); ?>:</td>
                        <td><?php echo esc_html($_SERVER['SERVER_ADDR']); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Web Port', 'wpat' ); ?>:</td>
                        <td><?php echo $_SERVER['SERVER_PORT']; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Date / Time (WP)', 'wpat' ); ?>:</td>
                        <td><?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) ) . ' (' . current_time( 'mysql' ) . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Timezone (WP)', 'wpat' ); ?>:</td>
                        <td><?php echo date_default_timezone_get() . ' (' . $common->wp_timezone() . ')'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Default Timezone is UTC', 'wpat' ); ?>:</td>
                        <td>
                            <?php $default_timezone = date_default_timezone_get();
                            if( 'UTC' !== $default_timezone ) {
                                echo $no . sprintf( __( 'Default timezone is %s - it should be UTC', 'wpat' ), $default_timezone ) . '</span>';
                            } else {
                                echo $yes;
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Protocol', 'wpat' ); ?>:</td>
                        <td><?php echo php_uname('n'); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Administrator', 'wpat' ); ?>:</td>
                        <td><?php echo $_SERVER['SERVER_ADMIN']; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'CGI Version', 'wpat' ); ?>:</td>
                        <td><?php echo $_SERVER['GATEWAY_INTERFACE']; ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'CPU Total', 'wpat' ); ?>:</td>
                        <td><?php echo $common->check_cpu_count() . ' / ' . $common->check_core_count() . ' ' . esc_html__( 'Cores', 'wpat' ); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'CPU Usage', 'wpat' ); ?>:</td>
                        <td><div class="status-progressbar"><span><?php echo $common->getServerCpuLoadPercentage() . '% '; ?></span><div style="width: <?php echo $common->getServerCpuLoadPercentage(); ?>%"></div></div></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'CPU Load Average', 'wpat' ); ?>:</td>
                        <td><?php echo $common->cpu_load_average(); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Disk Space', 'wpat' ); ?>:</td>
                        <td><?php echo esc_html__( 'Total', 'wpat' ) . ': ' . $common->getServerDiskSize()['size'] . ' GB / ' . esc_html__( 'Free', 'wpat' ) . ': ' . $common->getServerDiskSize()['free'] . ' GB / ' . esc_html__( 'Used', 'wpat' ) . ': ' . $common->getServerDiskSize()['used'] . ' GB'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Disk Space Usage', 'wpat' ); ?>:</td>
                        <td>
                            <div class="status-progressbar"><span><?php echo $common->getServerDiskSize()['usage'] . '% '; ?></span><div style="width: <?php echo $common->getServerDiskSize()['usage']; ?>%"></div></div>
                            <?php echo ' ' . $common->getServerDiskSize()['used'] . ' GB of ' . $common->getServerDiskSize()['size'] . ' GB'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Memory (RAM) Total', 'wpat' ); ?>:</td>
                        <td><?php echo $common->getServerRamDetail()['MemTotal'] . ' GB'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Memory (RAM) Free', 'wpat' ); ?>:</td>
                        <td><?php echo $common->getServerRamDetail()['MemFree'] . ' GB'; ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Memory (RAM) Usage', 'wpat' ); ?>:</td>
                        <td>
                            <div class="status-progressbar"><span><?php echo $common->getServerRamDetail()['MemUsagePercentage'] . '% '; ?></span><div style="width: <?php echo $common->getServerRamDetail()['MemUsagePercentage']; ?>%"></div></div>
                            <?php echo ' ' . $common->getServerRamDetail()['MemTotal'] - $common->getServerRamDetail()['MemFree'] . ' GB of ' . $common->getServerRamDetail()['MemTotal'] . ' GB'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Memcached', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( extension_loaded( 'memcache' ) ) : 
                                echo $yes;
                            else :
                                echo $no;
                            endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Uptime', 'wpat' ); ?>:</td>
                        <td><?php echo $common->getServerUptime(); ?></td>
                    </tr>
                    <tr class="table-border-top">
                        <td><?php esc_html_e( 'PHP Version', 'wpat' ); ?>:</td>
                        <td><?php echo $common->getPhpVersion(); ?></td>
                    </tr>
                    <?php if( function_exists('ini_get') ) : ?>
                        <tr>
                            <td><?php esc_html_e( 'PHP Memory Limit (WP)', 'wpat' ); ?>:</td>
                            <td><?php echo $common->getServerWPMemoryLimit(); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Memory Usage', 'wpat' ); ?>:</td>
                            <td>
                                <?php if( $common->server_memory_usage()['MemLimitGet'] == '-1' ) { ?>
                                    <?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . esc_html__( 'Unlimited', 'wpat' ) . ' (-1)'; ?>
                                <?php } else { ?>
                                    <div class="status-progressbar"><span><?php echo $common->server_memory_usage()['MemUsageCalc'] . '% '; ?></span><div style="width: <?php echo $common->server_memory_usage()['MemUsageCalc']; ?>%"></div></div>
                                    <?php echo $common->server_memory_usage()['MemUsageFormat'] . ' ' . esc_html__( 'of', 'wpat' ) . ' ' . $common->server_memory_usage()['MemLimitFormat']; ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Max Upload Size (WP)', 'wpat' ); ?>:</td>
                            <td><?php echo (int)ini_get('upload_max_filesize') . ' MB (' . size_format( wp_max_upload_size() ) . ')'; ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Post Max Size', 'wpat' ); ?>:</td>
                            <td><?php echo size_format( $common->memory_size_convert( ini_get( 'post_max_size' ) ) ); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Max Input Vars', 'wpat' ); ?>:</td>
                            <td><?php echo ini_get('max_input_vars'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Max Execution Time', 'wpat' ); ?>:</td>
                            <td><?php echo ini_get('max_execution_time') . ' ' . esc_html__( 'Seconds', 'wpat' ); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Extensions', 'wpat' ); ?>:</td>
                            <td><?php echo esc_html( implode(', ', get_loaded_extensions() ) ); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'GD Library', 'wpat' ); ?>:</td>
                            <td>
                                <?php $gdl = gd_info(); 
                                if( $gdl ) {
                                    echo $yes . ' / ' . esc_html__( 'Version', 'wpat' ) . ': ' . $gdl['GD Version'];
                                } else { 
                                    echo $no; 
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'cURL Version', 'wpat' ); ?>:</td>
                            <td><?php echo $common->getcURLVersion(); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'SUHOSIN Installed', 'wpat' ); ?>:</td>
                            <td><?php echo extension_loaded('suhosin') ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if( function_exists('ini_get') ) : ?>
                        <tr>
                            <td><?php esc_html_e('PHP Error Log File Location', 'wpat'); ?>:</td>
                            <td><?php echo ini_get('error_log'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php $fields = array();

                    // fsockopen/cURL.
                    $fields['fsockopen_curl']['name'] = 'fsockopen/cURL';

                    if( function_exists('fsockopen') || function_exists('curl_init') ) {
                        $fields['fsockopen_curl']['success'] = true;
                    } else {
                        $fields['fsockopen_curl']['success'] = false;
                    }

                    // SOAP.
                    $fields['soap_client']['name'] = 'SoapClient';

                    if( class_exists('SoapClient') ) {
                        $fields['soap_client']['success'] = true;
                    } else {
                        $fields['soap_client']['success'] = false;
                        $fields['soap_client']['note'] = sprintf(__('Your server does not have the %s class enabled - some gateway plugins which use SOAP may not work as expected.', 'bsi'), '<a href="https://php.net/manual/en/class.soapclient.php">SoapClient</a>');
                    }

                    // DOMDocument.
                    $fields['dom_document']['name'] = 'DOMDocument';

                    if( class_exists('DOMDocument') ) {
                        $fields['dom_document']['success'] = true;
                    } else {
                        $fields['dom_document']['success'] = false;
                        $fields['dom_document']['note'] = sprintf(__('Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'bsi'), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>');
                    }

                    // GZIP.
                    $fields['gzip']['name'] = 'GZip';

                    if( is_callable('gzopen') ) {
                        $fields['gzip']['success'] = true;
                    } else {
                        $fields['gzip']['success'] = false;
                        $fields['gzip']['note'] = sprintf(__('Your server does not support the %s function - this is required to use the GeoIP database from MaxMind.', 'bsi'), '<a href="https://php.net/manual/en/zlib.installation.php">gzopen</a>');
                    }

                    // Multibyte String.
                    $fields['mbstring']['name'] = 'Multibyte String';

                    if( extension_loaded('mbstring') ) {
                        $fields['mbstring']['success'] = true;
                    } else {
                        $fields['mbstring']['success'] = false;
                        $fields['mbstring']['note'] = sprintf(__('Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'bsi'), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>');
                    }

                    // Remote Get.
                    $fields['remote_get']['name'] = 'Remote Get Status';

                    $response = wp_remote_get('https://www.paypal.com/cgi-bin/webscr', array(
                        'timeout' => 60,
                        'user-agent' => 'BSI/' . 1.0,
                        'httpversion' => '1.1',
                        'body' => array(
                            'cmd' => '_notify-validate'
                        )
                    ));
                    $response_code = wp_remote_retrieve_response_code($response);
                    if( $response_code == 200 ) {
                        $fields['remote_get']['success'] = true;
                    } else {
                        $fields['remote_get']['success'] = false;
                    }

                    foreach( $fields as $field ) {
                        $mark = ! empty( $field['success'] ) ? 'yes' : 'error'; ?>
                        <tr>
                            <td data-export-label="<?php echo esc_html( $field['name'] ); ?>"><?php echo esc_html( $field['name'] ); ?>:</td>
                            <td>
                                <span class="<?php echo $mark; ?>">
                                    <?php echo ! empty( $field['success'] ) ? $yes : $no; ?> <?php echo ! empty( $field['note'] ) ? wp_kses_data( $field['note'] ) : ''; ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
                    
            <h2><?php echo esc_html__( 'Current Theme', 'wpat' ); ?></h2>
                
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Info', 'wpat' ); ?></th>
                        <th class="manage-column"><?php echo esc_html__( 'Result', 'wpat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );
                    $active_theme = wp_get_theme();
                    $theme_version = $active_theme->Version; ?>
                    <tr>
                        <td><?php esc_html_e( 'Name', 'wpat' ); ?>:</td>
                        <td><?php echo esc_html( $active_theme->Name ); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Version', 'wpat' ); ?>:</td>
                        <td>
                            <?php echo esc_html( $theme_version ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Author URL', 'wpat' ); ?>:</td>
                        <td><?php echo $active_theme->{ 'Author URI' }; ?></td>
                    </tr>                    
                    <tr>
                        <td><?php esc_html_e( 'Image Sizes', 'wpat' ); ?>:</td>
                        <td><?php echo implode( ', ', get_intermediate_image_sizes() ); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Gutenberg Compatibility', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( current_theme_supports( 'editor-color-palette' ) && current_theme_supports( 'align-wide' ) ) { 
                                echo $yes; 
                            } else {
                                echo $no; 
                            } ?>
                        </td>
                    </tr>           
                    <tr>
                        <td><?php esc_html_e( 'WooCommerce Compatibility', 'wpat' ); ?>:</td>
                        <td>
                            <?php if( current_theme_supports( 'woocommerce' ) ) { 
                                echo $yes; 
                            } else {
                                echo $no; 
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'Child Theme', 'wpat' ); ?>: <a href="https://codex.wordpress.org/Child_Themes" target="_blank"><?php echo $help; ?></a></td>
                        <td>
                            <?php echo is_child_theme() ? '<span class="yes"><span class="dashicons dashicons-yes"></span>Yes</span>' : '<span class="warning"><span class="dashicons dashicons-warning"></span> No. ' . sprintf(__('If you\'re want to modifying a theme, it safe to create a child theme.  See: <a href="%s" target="_blank">How to create a child theme</a>', 'wpat'), 'https://codex.wordpress.org/Child_Themes') . '</span>'; ?>
                        </td>
                    </tr>
                    <?php if( is_child_theme() ) :
                        $parent_theme = wp_get_theme( $active_theme->Template ); ?>
                        <tr>
                            <td><?php esc_html_e( 'Parent Theme Name', 'wpat' ); ?>:</td>
                            <td><?php echo esc_html( $parent_theme->Name ); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Parent Theme Version', 'wpat' ); ?>:</td>
                            <td>
                                <?php echo esc_html( $parent_theme->Version );
                                if( version_compare( $parent_theme->Version, $update_theme_version, '<') ) {
                                    echo ' &ndash; <strong style="color:red;">' . sprintf( __('%s is available', 'wpat'), esc_html( $update_theme_version ) ) . '</strong>';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Parent Theme Author URL', 'wpat' ); ?>:</td>
                            <td><?php echo $parent_theme->{ 'Author URI' }; ?></td>
                        </tr>
                    <?php endif ?>                             
                </tbody>
            </table>
            
            <h2><?php echo esc_html__( 'Active Plugins', 'wpat' ); ?></h2>
                
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Name', 'wpat' ); ?></th>
                        <th class="manage-column"><?php echo esc_html__( 'Version', 'wpat' ); ?></th>
                        <th width="25%" class="manage-column"><?php echo esc_html__( 'Author', 'wpat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $active_plugins = (array) get_option( 'active_plugins', array() );

                    if( is_multisite() ) {
                        $network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
                        $active_plugins = array_merge( $active_plugins, $network_activated_plugins );
                    }

                    foreach ( $active_plugins as $plugin ) {

                        $plugin_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                        $dirname = dirname( $plugin );
                        $version_string = '';
                        $network_string = '';

                        if( ! empty( $plugin_data['Name'] ) ) {

                            // Link the plugin name to the plugin url if available.
                            $plugin_name = esc_html( $plugin_data['Name'] );

                            if( ! empty( $plugin_data['PluginURI'] ) ) {
                                $plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage', 'wpat' ) . '" target="_blank">' . $plugin_name . '</a>';
                            }

                            if( strstr( $dirname, 'wpat-' ) && strstr( $plugin_data['PluginURI'], 'woothemes.com' ) ) {

                                if( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
                                    $changelog = wp_safe_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
                                    $cl_lines = explode( "\n", wp_remote_retrieve_body( $changelog ) );
                                    if( ! empty( $cl_lines ) ) {
                                        foreach ( $cl_lines as $line_num => $cl_line ) {
                                            if( preg_match( '/^[0-9]/', $cl_line ) ) {
                                                $date = str_replace( '.', '-', trim( substr( $cl_line, 0, strpos( $cl_line, '-' ) ) ) );
                                                $version = preg_replace( '~[^0-9,.]~', '', stristr( $cl_line, "version" ) );
                                                $update = trim(str_replace( "*", "", $cl_lines[$line_num + 1] ) );
                                                $version_data = array( 'date' => $date, 'version' => $version, 'update' => $update, 'changelog' => $changelog );
                                                set_transient( md5( $plugin ) . '_version_data', $version_data, DAY_IN_SECONDS );
                                                break;
                                            }
                                        }
                                    }
                                }

                                if( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) ) {
                                    $version_string = ' &ndash; <strong style="color:red;">' . esc_html( sprintf( _x('%s is available', 'Version info', 'wpat'), $version_data['version'] ) ) . '</strong>';
                                }

                                if( $plugin_data['Network'] != false ) {
                                    $network_string = ' &ndash; <strong style="color:black;">' . __( 'Network enabled', 'wpat' ) . '</strong>';
                                }
                            } ?>
                            <tr>
                                <td><?php echo $plugin_name; ?></td>
                                <td><?php echo esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
                                <td><?php echo sprintf( _x('%s', 'by author', 'wpat' ), $plugin_data['Author'] ); ?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
            
            <br><br>
            
        </div>

    <?php }

endif;
    
?>