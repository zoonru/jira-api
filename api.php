<?php

if (!is_file('config.php')) {
    echo "Please, add config.php file! It should consists $login and $api_key for your jira account.";
    exit;
}

$loader = require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

$shortopts = "";
$shortopts .= "a:"; //action

//create params
$shortopts .= "k:"; //feature key
$shortopts .= "d:"; //dev task
$shortopts .= "f:"; //front task

//report params
$shortopts .= "t:"; //team_lead
$shortopts .= "s:"; //sprints

$options = getopt($shortopts);

$Jira = \Badoo\Jira\REST\Client::instance();
$Jira
    ->setJiraUrl('https://zoonru.atlassian.net')
    ->setAuth($login, $api_key);

if ($options['a'] && $options['a'] === 'create') {
    $create = new Zoon\Jira\CreateTasks($Jira);

    if ($options['k']) {
        $feature_key = "{$options['k']}";
    } else {
        echo 'for "-a=create" option "-k" is required and it\'s value should be the feature key';
        exit();
    }

    if ($options['s']) {
        $sprint = (int) $options['s'];
    } else {
        echo 'for "-a=create" option "-s" is required and it\'s value should be the sprint id';
        exit();
    }

    if ($options['d']) {
        $dev_task_count = (int) $options['d'];
        if ($dev_task_count > 0) {
            $create->createTask($feature_key, 'd', $dev_task_count, $sprint);
            echo "Created {$dev_task_count} dev task(s) for the feature {$feature_key}";
        } else {
            echo 'Wrong value for "-d" option';
            exit;
        }
    }

    if ($options['f']) {
        $front_task_count = (int) $options['f'];
        if ($front_task_count > 0) {
            $create->createTask($feature_key, 'f', $front_task_count, $sprint);
            echo "Created {$front_task_count} front task(s) for the feature {$feature_key}";
        } else {
            echo 'Wrong value for "-f" option';
            exit;
        }
    }

} elseif ($options['a'] && $options['a'] === 'report') {
    if ($options['s']) {
        $sprint = "({$options['s']})";
    } else {
        echo 'for "-a report" option "-s" is required and it\'s value should be the sprint id';
        exit;
    }

    if ($options['t']) {
        switch ($options['t']) {
            case 'a':
                $teamLead = '("aktuba@yandex.ru", "557058:0ede0b5d-f381-4475-a261-08e136230444")';
                break;
            case 's':
                $teamLead = '(5be147d8a86b3349680e6120)';
                break;
            case 'n':
                $teamLead = '(5d760a4907c82b0d8fa765d3)';
                break;
            default:
                echo 'Wrong value for "-t" option';
        }

    } else {
        echo 'for "-a report" option "-t" is required and it\'s value should be the team lead';
        exit;
    }

    $report = new Zoon\Jira\CreateReport();
    $report->report($sprint, $teamLead);
} else {
    echo 'Param "-a" is required and it\'s value should be "create" or "report"';
    exit;
}

exit;