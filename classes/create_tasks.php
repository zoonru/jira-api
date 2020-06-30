<?php

namespace Zoon\Jira;

Class Priority
{
    public $name;
}

class CreateTasks
{
    public function __construct($Jira) {

    }

    public function createTask($featureKey, $type = 'd', $new_tasks_count = 1, $sprint): array {
        $new_tasks = array();
        $feature = new \Badoo\Jira\Issue($featureKey);

        //$feature->edit('customfield_10113', array('set' => 318));
        //$feature->save();
        //var_dump($feature->getHistory()); exit();

        //var_dump(\Badoo\Jira\User::get($feature->getFieldValue('customfield_10742')->displayName)); exit;

        print $feature->getKey();
        print $feature->getSummary();

        if ($type == 'd') {
            $taskType = 'Dev Task';
        } elseif ($type == 'f') {
            $taskType = 'Front task';
        } elseif  ($type == 'b') {
            $taskType = 'Bug';
        } else {
            echo 'Wront type parameter: ' . $type;
            exit;
        }

        for ($i = 1; $i<=$new_tasks_count; $i++) {
            $Request = new \Badoo\Jira\Issue\CreateRequest('MAIN', $taskType);

            $priority = new Priority();
            $priority->name = $feature->getPriority()->getName();

            $Request
                ->setSummary($feature->getSummary())
                ->setFieldValue('Описание', 'См. фичу')
                ->setFieldValue('Исполнитель', $feature->getFieldValue('customfield_10742'))
                ->setFieldValue('Приоритет', $priority)
                //team lead
                ->setFieldValue('customfield_10742', $feature->getFieldValue('customfield_10742'))
                //product
                ->setFieldValue('customfield_10743', $feature->getFieldValue('customfield_10743'))
                //epic
                ->setFieldValue('customfield_10006', $feature->getFieldValue('customfield_10006'))
                //sprint
                ->setFieldValue('customfield_10113', $sprint);

            $Issue = $Request->send();

            print_r(
                [
                    'key'           => $Issue->getKey(),
                    'summary'       => $Issue->getSummary(),
                ]
            );

            \Badoo\Jira\Issue\Link::create('is-subtask-of', $feature->getKey(), $Issue->getKey());

            $new_tasks[] = $Issue->getKey();
        }

        return $new_tasks;
    }

    public function createAllSprintTasks(): array {
        $sprint = 332;
        //andrey
        //$team_lead = '("aktuba@yandex.ru", "557058:0ede0b5d-f381-4475-a261-08e136230444")';
        //sasha
        //$team_lead = '(5be147d8a86b3349680e6120)';
        //nikita
        $team_lead = '(5d760a4907c82b0d8fa765d3)';


        $jql = "PROJECT = Main AND Sprint = {$sprint} AND type = \"Product Feature\" AND \"Team Lead\" IN {$team_lead} AND status=\"Ready for development\"";
        $sprint = \Badoo\Jira\Issue::search($jql);
        $created = array();

        foreach ($sprint as $issue) {
            if ($issue->getKey() != 'MAIN-12041') {
                $created[] = $this->createTask($issue->getKey());
            }
        }

        var_dump($created);

        return $created;
    }
}
