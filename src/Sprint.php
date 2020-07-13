<?php

namespace Zoon\Jira;

class Sprint
{
    /** @var string[]  */
    private static $sprint = [];

    public static function isIssueInSprint(string $issueKey, string $sprint, string $teamLead): bool {
        if (count(self::$sprint) === 0) {
            self::fillSprint($sprint, $teamLead);
        }

        return in_array($issueKey, self::$sprint);
    }

    private static function fillSprint(string $sprint, string $teamLead): void {
        $jql = "PROJECT = Main AND Sprint IN {$sprint} AND 'Team Lead' IN {$teamLead}";
        $sprint = \Badoo\Jira\Issue::search($jql);

        foreach ($sprint as $issue) {
            self::$sprint[] = $issue->getKey();
        }
    }
}
