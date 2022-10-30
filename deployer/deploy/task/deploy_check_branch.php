<?php

namespace Deployer;

use Deployer\Type\Csv;
use Deployer\Exception\GracefulShutdownException;

// Read more on https://github.com/sourcebroker/deployer-extended#deploy-check-branch
task('deploy:check_branch', function () {
    $branchCheck = true;
    if (input()->hasOption('tag')) {
        $tag = input()->getOption('tag');
        if (!empty($tag)) {
            $branchCheck = false;
        }
    }
    if (empty($tag) && input()->hasOption('revision')) {
        $revision = input()->getOption('revision');
        if (!empty($revision)) {
            $branchCheck = false;
        }
    }
    if ($branchCheck) {
        cd('{{deploy_path}}');
        $branchToBeDeployed = get('branch');
        if (get('branch_set_explicitly', true) && empty($branchToBeDeployed)) {
            writeln('No branch to deploy detected. Set the branch you want to deploy explicitly by setting ->set("branch", "master"); or by adding cli param "--branch="');
            throw new GracefulShutdownException('Process aborted.');
        }
        if (test('[ -e .dep/releases.extended ]')) {
            $csv = run('tail -n 1 .dep/releases.extended');
            if ($csv) {
                $metainfo = Csv::parse($csv)[0];
                if (isset($metainfo[2]) && isset($metainfo[3]) && isset($metainfo[5])) {
                    $date = \DateTime::createFromFormat('YmdHis', $metainfo[0])->format('Y-m-d H:i:s');
                    $currentRemoteBranch = $metainfo[2];
                    $userName = $metainfo[3];
                    $type = $metainfo[5];
                    if ($type == 'branch' && !empty($currentRemoteBranch) && $currentRemoteBranch != $branchToBeDeployed) {
                        if (!askConfirmation(sprintf(
                            'On host "%s" there is currently branch "%s" deployed by "%s" on %s. ' .
                            'You are trying to deploy now branch "%s". Do you really want to continue?',
                            get('argument_stage'),
                            $currentRemoteBranch,
                            $userName,
                            $date,
                            $branchToBeDeployed
                        ), false)) {
                            throw new GracefulShutdownException('Process aborted.');
                        }
                    }
                }
            }
        }
    }
})->desc('Check if the branch we want to deploy is equal to the branch that has been already deployed');
