#!/usr/bin/php
<?php

/**
 * To run locally call build_up.
 * lando php scripts/tfa/build_up
 */

// Set to false to inhibit code sections for debugging.
define('CREATE_MULTIDEV_IF_NEEDED', TRUE);
define('BUILD_ASSETS', TRUE);
define('MERGE_CHANGES', TRUE);
define('UPLOAD_BUILD_OUTPUT', TRUE);
define('UPDB_CIM_CR', TRUE);
$ENV = 'web-999';

$git_hub_ref_name = trim(`printenv GITHUB_REF_NAME`);
echo "Target Branch: $git_hub_ref_name\r\n";

$ENV = $git_hub_ref_name;

if (constant('MERGE_CHANGES') == TRUE) {

/*
  Pre existing code. may need to be different for local and githib
  // Form the refspec to go from and to the same branchname.
  $refspec = $git_hub_ref_name . ':' . $git_hub_ref_name;
  echo "refspec: $git_hub_ref_name\r\n";
*/

  // Noticed it did not like the name I was providing for the sourc branch. It prefers HEAD.
  echo "Start Waiting - post merge\r\n";
  //wait_for_workflow($ENV, 'Sync code on', 600);
  //check_workflow_status($ENV, 600, 30);
  echo "Done Waiting - post merge\r\n";

}
else {
  echo "Merging of changes inhibited by BUILD_ASSETS.\r\n\r\n";
}


/**
 * Determines if a workflow is still running in an environment.
 */
function is_workflow_running($environment, $workflow_title) {
  $workflows_json = `terminus workflow:list teachforamerica --format=json`;

  $workflows = json_decode($workflows_json);
  foreach($workflows as $workflow) {
    // Only the ones in the specified environment.
    if ($workflow->env == $environment) {

      // Only the ones with the correct title.
      if (strncmp($workflow->workflow, $workflow_title, strlen($workflow_title) ) == 0) {

        // If status is running return TRUE
        //if ($workflow->status == 'running') {
        if (strcmp($workflow->status, 'running') == 0) {

          return TRUE;
        }

      }

    }

  }

  return FALSE;
}



function wait_for_workflow($environment, $workflow_title, $timeout=600) {
  $start_time = time();

  do {
    // Go to sleep for a few seconds and see if worflow is still running.
    sleep(30);

    // Do something.
    if (is_workflow_running($environment, $workflow_title) == FALSE) {
      echo "$workflow_title in '$environment' COMPLETED\r\n";
      return;
    }
    else {
      echo "$workflow_title in '$environment' RUNNING\r\n";
    }

  } while (($start_time + $timeout) < time() );
}
