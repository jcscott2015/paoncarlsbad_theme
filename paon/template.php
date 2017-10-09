<?php
// $Id: template.php,v 1.0 2009/05/14 11:47:37 jcscott Exp $

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' › ', $breadcrumb) .'</div>';
  }
}

/**
 * Allow themable wrapping of all comments.
 */
function phptemplate_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    return '<div id="comments"><h2 class="comments">'. t('Comments') .'</h2>'. $content .'</div>';
  }
}

/**
* Override or insert variables into the node templates.
*
* @param $vars
*   An array of variables to pass to the theme template.
*/
function phptemplate_preprocess_node(&$vars) {
  // Enable node templates based on node id.
  $vars['template_files'][] = 'node-' . $vars['nid'];

  // Enable node templates based on taxonomy term.
  $node = $vars['node'];
  if (arg(0) == 'taxonomy') {
    $taxonomy = $node->taxonomy;
    $terms = array();
    foreach ( $taxonomy as $term ) { $terms[] = $term->tid; }
    $term_str = implode('-', $terms);
	$vars['template_files'][] = 'node-taxonomy-' . $term_str;
  }

  // If we have any terms, let's eliminate all but the Categories (vid 5) terms for display.
  if ($vars['node']->taxonomy) {
    // Let's iterate through each term.
    foreach ($vars['node']->taxonomy as $term) {
      // We will build a new array where there will be as many
      // nested arrays as there are vocabularies, but without vid 6.
      // The key for each nested array is the vocabulary ID.     
      if ( ($term->vid == 5) || (taxonomy_vocabulary_load($term->vid)->name == 'Categories') ) {
		$vocabulary[$term->vid]['taxonomy_term_'. $term->tid] = array(
		  'title' => $term->name,
		  'href' => taxonomy_term_path($term),
		  'attributes' => array(
			'rel' => 'tag', 
			'title' => strip_tags($term->description),
		  ),
		);
      }
    }

	// We will get rid of the old $terms variable.
	unset($vars['terms']);

    // And build a new $terms.
	if(isset($vocabulary)) {
	  // Making sure vocabularies appear in the same order.
	  ksort($vocabulary, SORT_NUMERIC);
	  if($vars['node']->type == 'story') {
		$term_list = array();
		foreach ($vocabulary as $vid => $terms) {
		  // Add a link to the terms array.
		  foreach ($terms as $el) $term_list[] = l($el['title'], $el['href'], array('attributes'=>$el['attributes']));
		}
		$vars['terms'] = implode(", ", $term_list);
	  } else {
		foreach ($vocabulary as $vid => $terms) {
		  // Using the theme_links(...) function to theme terms list.
		  $vars['terms'] .= theme_links($terms, array('class' => 'links inline'));
		}
	  }
	}
  }    

  return $vars;
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_page(&$vars) {
  // Set site email vars.
  $vars['site_email'] = variable_get('site_mail', ini_get('sendmail_from'));
  $vars['site_email_link'] = '<a href="mailto:'.$vars['site_email'].'">'.$vars['site_email'].'</a>';
  
  $vars['tabs2'] = menu_secondary_local_tasks();

  // If in node edit mode, I must be an admin and I need to see tabs and a specially formatted title.
  $vars['admin_mode'] = ((arg(0) == 'node') && (arg(2))) ? TRUE : FALSE;

  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }

  // use custom page tpls by nodetype
  if ($node = menu_get_object()) {
    $vars['node'] = $node;
	$suggestions = array();
	$template_filename = 'page';
	$template_filename = $template_filename . '-' . $vars['node']->type;
    $suggestions[] = $template_filename;
    $vars['template_files'] = $suggestions;
  }

  // Add page template suggestions based on the aliased path.
  // For instance, if the current page has an alias of about/history/early,
  // we'll have templates of:
  // page-about-history-early.tpl.php
  // page-about-history.tpl.php
  // page-about.tpl.php
  // Whichever is found first is the one that will be used.
  if (module_exists('path')) {
    //$alias = drupal_get_path_alias(str_replace('/edit','',$_GET['q']));
    $alias = drupal_get_path_alias($_GET['q']);
    if ($alias != $_GET['q']) {
      $suggestions = array();
      $template_filename = 'page';
      foreach (explode('/', $alias) as $path_part) {
        $template_filename = $template_filename . '-' . $path_part;
        $suggestions[] = $template_filename;
      }
      $vars['template_files'] = array_merge((array) $suggestions, $vars['template_files']);
    }
  }
}

/**
 * Pull in Webform to insert in pages.
 */
function get_signIn_form($nid) {
	$node = node_load($nid);
	$node->title = NULL;
	return node_view($node);
}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function phptemplate_comment_submitted($comment) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Generates IE CSS links for LTR and RTL languages.
 */
function phptemplate_get_ie_styles() {
  global $language;

  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />';
  if ($language->direction == LANGUAGE_RTL) {
    $iecss .= '<style type="text/css" media="all">@import "'. base_path() . path_to_theme() .'/fix-ie-rtl.css";</style>';
  }

  return $iecss;
}
