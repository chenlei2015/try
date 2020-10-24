<?php
require "../../../curl/Curl.php";

class timer
{
    //private $host = "http://www.tms-b.com/";
    private $host = "www.tms-b.com/";
    //private $host = "http://192.168.71.141:92/";

    //private $php_cmd = "D:\code\phpstudy\PHPTutorial\php\php-7.1.13-nts\php  D:\code\phpstudy\PHPTutorial\WWW\\tms\appdal\index.php ";
    private $php_cmd = "php  D:\code\phpstudy\PHPTutorial\WWW\\tms\appdal\index.php ";

    private $taskString;

    private $min;

    private $hour;

    private $day;

    private $month;

    private $week;

    private $command;

    private $process;

    private $runTime;

    private $first_min = 0;
    private $last_min  = 59;

    private $first_hour = 0;
    private $last_hour  = 23;

    private $first_day = 1;
    private $last_day  = 31;

    private $first_month = 1;
    private $last_month  = 12;

    private $first_week  = 0;
    private $last_week   = 6;

    /**
     * @var string $taskString example: 10 * * * * php example.php
     */
    public function __construct(string $taskString,$url)
    {
        $this->taskString = $taskString;
        $this->command = $url;
        $this->runTime = time();
        $this->initialize();

        //$reflect = new ReflectionClass(__CLASS__);
        //$pros = $reflect->getProperties();
    }



    /**
     * 初始化任务配置
     */
    private function initialize()
    {
        //过滤多余的空格
        $rule = array_filter(explode(" ", $this->taskString), function($value) {
            return $value != "";
        });
        if (count($rule) < 5) {
            throw new ErrorException("'taskString' parse failed");
        }
        $this->min   = $this->format($rule[0], 'min');
        $this->hour  = $this->format($rule[1], 'hour');
        $this->day   = $this->format($rule[2], 'day');
        $this->month = $this->format($rule[3], 'month');
        $this->week  = $this->format($rule[4], 'week');
    }

    private function format($value, $field)
    {
        if ($value === '*') {
            return $value;
        }

        if (is_numeric($value)) {
            return [$this->checkFieldRule($value, $field)];
        }

        $steps = explode(',', $value);
        $scope = [];
        foreach ($steps as $step) {

            if (strpos($step, '/') !== false) {
                $inter = explode('/', $step);
                if(strpos($inter[0], '-') !== false){
                    $range = explode('-', $inter[0]);
                    $container = array_merge($scope, range(
                        $this->checkFieldRule($range[0], $field),
                        $this->checkFieldRule($range[1], $field)
                    ));
                    foreach ($container as $v){
                        if($v%$inter[1] == 0){
                            $scope[] = intval($v);
                        }
                    }
                }else{
                    $confirmInter = isset($inter[1]) ? $inter[1] : $inter[0];
                    if ($confirmInter === '/') {
                        $confirmInter = 1;
                    }
                    $scope = array_merge($scope, range(
                        $this->{"first_". strtolower($field)},
                        $this->{"last_". strtolower($field)},
                        $confirmInter
                    ));
                }
                continue;
            }


            if (strpos($step, '-') !== false) {
                $range = explode('-', $step);
                $scope = array_merge($scope, range(
                    $this->checkFieldRule($range[0], $field),
                    $this->checkFieldRule($range[1], $field)
                ));
                continue;
            }


            $scope[] = intval($step);
        }

        return $scope;
    }


    private function checkFieldRule($value, $field)
    {
        $first_const = 'first_' . strtolower($field);
        $last_const  = 'last_' . strtolower($field);
        $first = $this->{$first_const};
        $last  = $this->{$last_const};

        if ($value < $first) {
            return $first;
        }
        if ($value > $last) {
            return $last;
        }
        return (int) $value;
    }


    public function getTimeAttribute($attribute)
    {
        if (!in_array($attribute, ['min', 'hour', 'day', 'month', 'week', 'runTime'])) return null;
        return $this->{$attribute} ?? null;
    }

    public function setRunTime($time)
    {
        $this->runTime = $time;
    }

    public function run_cli()
    {
        $command = $this->php_cmd.$this->command;
        //shell_exec('cd ../../../../../tms/appdal && php index.php ordersys console ShipFee test');
        exec($command);
    }

    public function run_curl()
    {
        if (null === $this->process) {
            $this->process = new Curl();
        }
        $url = $this->host.$this->command;
        $this->process->requestByCurlGet($url);
    }

}