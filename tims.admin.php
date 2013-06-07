<?php

function tims_edit($form, &$form_state, $template_name) {
  $templates = variable_get('tims_templates', array());
  $template = '';
  if (array_key_exists($template_name, $templates)) {
    $template = $templates[$template_name];
  }

  $form['name'] = array(
    '#title' => 'Name',
    '#description' => 'The theme key for this template.',
    '#type' => 'textfield',
    '#default_value' => ($template_name != '_new_' ? $template_name : NULL),
    '#required' => 1,
    '#disabled' => ($template_name != '_new_'),
  );

  $form['template'] = array(
    '#title' => 'Template',
    '#description' => 'The actual TWIG template.',
    '#type' => 'textarea',
    '#default_value' => $template,
    '#required' => 1,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Save',
  );

  return $form;
}

function tims_edit_submit($form, &$form_state) {
  $templates = variable_get('tims_templates', array());
  $templates[$form_state['values']['name']] = $form_state['values']['template'];
  variable_set('tims_templates', $templates);
  $form_state['redirect'] = 'admin/structure/tims';
}

function tims_delete($form, &$form_state, $template_name) {
  $form['name'] = array(
    '#type' => 'hidden',
    '#value' => $template_name,
  );

  return confirm_form(
    $form,
    t('Are you sure you want to delete the template for "%tn"?', array('%tn' => $template_name)),
    'admin/structure/tims',
    t('This action cannot be undone.'),
    t('Delete Template')
  );
}

function tims_delete_submit($form, &$form_state) {
  $templates = variable_get('tims_templates', array());
  unset($templates[$form_state['values']['name']]);
  variable_set('tims_templates', $templates);
  $form_state['redirect'] = 'admin/structure/tims';
}

function tims_list($form, &$form_state) {
  $header = array(
    'name' => array(
      'data' => t('Name'),
      'field' => 'name',
    ),
    'operations' => t('Operations'),
  );

  $templates = variable_get('tims_templates', array());

  $rows = array();
  foreach ($templates as $name => $template) {
    $rows[$name] = array(
      'name' => $name,
      'operations' => array(
        'data' => array(
          '#theme' => 'links__node_operations',
          '#links' => array(
            'edit' => array(
              'title' => t('edit'),
              'href' => 'admin/structure/tims/' . $name . '/edit',
            ),
            'delete' => array(
              'title' => t('delete'),
              'href' => 'admin/structure/tims/' . $name . '/delete',
            ),
          ),
          '#attributes' => array('class' => array('links', 'inline')),
        ),
      ),
    );
  }

  $form['template_list'] = array(
    '#caption' => t('Existing Templates: ') . count($templates),
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => t('No templates have been created.'),
  );

  return $form;
}
