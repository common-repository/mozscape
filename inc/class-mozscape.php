<?php 
/**
 * The class that provides Mozscape API data for use with the WordPress SEO plugin.
 */
class Moz {
	
	function get_all_moz_data() {
		
		$content = '';
		
		if ($this->get_option_value('moz_url_metrics')) {
			$content .= $this->buildURLMetrics();
		}
		if ($this->get_option_value('moz_link_metrics')) {
			$content .= $this->buildLinkMetrics();
		}
		if ($this->get_option_value('moz_anchor_text_metrics')) {
			$content .= $this->buildAnchorTextMetrics();
		}
		
		if ($content === '') {
			$content = '<h2>Mozscape API Metrics</h2>';
			if ($this->get_option_value('moz_accessid') && $this->get_option_value('moz_secretkey')) {
				$content .= '<p>It looks like you haven\'t enabled any Mozscape Metrics yet.</p><p>You can enable Mozscape API Metrics in the <a href="' . admin_url( 'admin.php?page=mozscape_dashboard' ) . '">Mozscape Dashboard</a> area.</p>';
			} else {
				$content .= '<p>It looks like you haven\'t provided your Mozscape API details yet.</p><p>To enable Mozscape API Metrics, simply provide your Moz API Access ID and Secret Key in the <a href="' . admin_url( 'admin.php?page=mozscape_dashboard' ) . '">Mozscape Dashboard</a> area. Don\'t forget to select the metrics you would like to use.</p>';
			}
		}
		
		return $content;
	}
	
	function buildURLSafeSignature($access_id, $secret_key, $expires) {
		$stringToSign = $access_id."\n".$expires;
		$binarySignature = hash_hmac('sha1', $stringToSign, $secret_key, true);
		return urlencode(base64_encode($binarySignature));
	}
	
	function buildURL($url_base, $bit_flag_data) {
		$current_page = str_replace( 'http://', '', get_permalink() );
		$access_id = $this->get_option_value('moz_accessid');
		$secret_key = $this->get_option_value('moz_secretkey');
		$expires = time() + 300; // 5 minutes
		$urlSafeSignature = $this->buildURLSafeSignature($access_id, $secret_key, $expires);
		
		$url = $url_base . urlencode($current_page) . $bit_flag_data; 
		$url .= '&AccessID=' . $access_id;
		$url .= '&Expires=' . $expires;
		$url .= '&Signature=' . $urlSafeSignature;
		return $url;
	}
	
	function buildURLMetrics() {
		
		$content = '<h3>URL Metrics</h3>';
		
		$url = $this->buildURLMetricsURL();
		
		$results = $this->get_moz_data($url);
		$urlMetrics = json_decode($results);
		
		if (!empty($urlMetrics)) {
			$url_for_metrics = ($urlMetrics->uu == '') ? 'N/A' : $urlMetrics->uu;
			$links = intval($urlMetrics->uid);
			
			$juice_passing_external_links_url = intval($urlMetrics->ueid);
			$juice_passing_internal_external_links_url = intval($urlMetrics->ujid);
			$juice_passing_internal_links_url = intval($juice_passing_internal_external_links_url - $juice_passing_external_links_url);
			$mozrank_url = number_format(floatval($urlMetrics->umrp), 2);
			$moztrust_url = number_format(floatval($urlMetrics->utrp), 2);
			$page_authority_url = intval($urlMetrics->upa);
			$httpstatus_url = $urlMetrics->us;
	
			$content .= '<table cellpadding="5px">';
			$content .= '<tr><td>URL = ' .$url_for_metrics. '</td><td>MozRank = ' .$mozrank_url. '</td><td>MozTrust = ' .$moztrust_url. '</td></tr>';
			$content .= '<tr><td>Page Authority = ' .$page_authority_url. '</td><td colspan="2">HTTP Status Code = ' .$httpstatus_url. '</td></tr>';
			$content .= '<tr><td>Total Links = ' .$links. '</td><td>External Links = ' .$juice_passing_external_links_url. '</td><td>Internal Links = ' .$juice_passing_internal_links_url. '</td></tr>';
			$content .= '</table>';
		} else {
			$content .= '<p>There are no URL Metrics available for this page.</p>';
		}
		return $content;
	}
	
	function buildURLMetricsURL() {
		$url_base = 'http://lsapi.seomoz.com/linkscape/url-metrics/';
		$bit_flag_data = '?Cols=34896759076'; 
		return $this->buildURL($url_base, $bit_flag_data);
	}
		
	function buildExternalLinksURL() {
		$url_base = 'http://lsapi.seomoz.com/linkscape/links/';
		$bit_flag_data = '?SourceCols=6&Scope=page_to_page&Sort=page_authority&Filter=external+follow&Limit=10';
		return $this->buildURL($url_base, $bit_flag_data);
	}
		
	function buildExternalLinksToThisPage() {
		$content = '';
		$url = $this->buildExternalLinksURL();
		
		$results = $this->get_moz_data($url);
		$linkMetrics = json_decode($results, true);
		$external = array();
		$externalSameIPs = array();
		$content = '<div style="padding-left:1em;">';
		if (!empty($linkMetrics)) {
			$content .= '<h4>Top 10 External Links to this Page</h4><p>';
			foreach ($linkMetrics as $linkMetric) {
				$flag = intval($linkMetric['lf']);
				if ($flag === 24) {	
					$content .= $linkMetric['uu'] . ' (*)<br />';
				} elseif ($flag === 0) { 
					$content .= $linkMetric['uu'] . '<br />';
				} else { 
					$content .= $linkMetric['uu'] . ' (**)<br />';
				}
			}
			$content .= '</p>';
		} else {
			$content .= '<p>There is no external link data for this page.</p>';
		}
		
		$content .= '<p>* These links have the Same IP address as this page.</p></div>';
		
		return $content;
	}
	
	function buildLinkMetrics() {
		$content = '<h3>Link Metrics</h3>';
		$content .= $this->buildExternalLinksToThisPage();
		return $content;
	}
	
	function buildAnchorTextMetrics() {
		$content .= '<h3 style="clear:both;">Anchor Text Metrics</h3>';
		
		$url = $this->buildAnchorTextMetricsURL();
		
		$results = $this->get_moz_data($url);
		$phrases = json_decode($results);
		if (!empty($phrases)) {
			$content .= '<table cellpadding="5px">';
			$content .= '<tr><td><strong>Phrase</strong></td><td><strong>Internal Links</strong></td><td><strong>External Links</strong></td><td><strong>MozRank from Internal Links</strong></td><td><strong>MozRank from External Links</strong></td></tr>';
			foreach ($phrases as $phrase) {
				$content .= "<tr><td>$phrase->aput</td><td class=\"center\">$phrase->apuiu</td><td class=\"center\">$phrase->apueu</td><td class=\"center\">".number_format(floatval($phrase->apuimp), 2)."</td><td class=\"center\">".number_format(floatval($phrase->apuemp), 2)."</td></tr>";
			}
			$content .= '</table>';
		} else {
			$content .= '<p>There aren\'t any anchor text metrics by phrase for this page.</p>';
		}
		

		return $content;
	}
		
	function buildAnchorTextMetricsURL() {
		$url_base = 'http://lsapi.seomoz.com/linkscape/anchor-text/';
		$bit_flag_data = '?Cols=810&Scope=phrase_to_page&Sort=domains_linking_page&Limit=10';
		return $this->buildURL($url_base, $bit_flag_data);
	}
	
	/**
	 * Get the value for a Mozscape option by name.
	 * @param String $var
	 */
	function get_option_value($var) {
		$options = get_option( 'moz' );
		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );	
		return $val;
	}
	
	/**
	 * Get Moz data for this URL.
	 * @param String $url
	 */
	function get_moz_data($url) {
		$content = '';
		$options = array(CURLOPT_RETURNTRANSFER => TRUE);
		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	
}
?>