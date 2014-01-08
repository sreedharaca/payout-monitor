<?php

namespace Katana\OfferBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface as Container;


class DateToTextService {

    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    private function lexica($n,$a,$b,$c,$m=1)
    {
        $n=sprintf("%.0f",$n);
        $x=abs($n>9?substr($n,-2):$n);
        return ($m?($m==2?number_format($n,0,' ',' '):$n).' ':'').(($x%=100)>9&&$x<20||($x%=10)>4||$x==0?$c:($x==1?$a:$b));
    }

    public function dateToText($date, $mode=0)
    {

        if(!($date instanceof \DateTime)) return '';
        $date = $date->format('U');

        $months = array('','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
        if(preg_match('(^[0-9]+$)',$date))
        {
            if($mode == 2) return date('j ',$date).$months[date('n',$date)].date(' Y',$date).' г. в '.date('G:i', $date);
            elseif($mode == 3) return date('j ',$date).$months[date('n',$date)].date(' Y',$date).' г. в '.date('G:i:s', $date);
            $time = $mode ? '' : ' в '.date('G:i',$date);
            $delta = time() - $date;
            $future = $delta <= 0;
            $delta = abs($delta);
            if($delta <= 10) return $future ? 'Прямо сейчас' : 'Только что';
            elseif($delta < 60) return ($future?'Через ':'').$this->lexica($delta,'секунду','секунды','секунд').($future?'':' назад');
            elseif($delta <= 3300) return ($future?'Через ':'').(round($delta/60) == 1 ? ($future?'минуту':'Минуту') : $this->lexica(round($delta/60),'минуту','минуты','минут')).($future?'':' назад');
            elseif(date('Ymd',$date) == date('Ymd')) return $delta % 3600 >= 3300 || ($delta % 3600 <= 300 && $delta >= 3300) ? ($future?'Через ':'').(round($delta/3600) == 1 ? ($future?'час':'Час') : $this->lexica(round($delta/3600),'час','часа','часов')).($future?'':' назад') : 'Сегодня'.$time;
            elseif(date('Ym',$date) == date('Ym')) { if($delta % 604800 >= 561600 || ($delta % 604800 <= 43200 && $delta >= 561600)) return ($future?'Через ':'').(round($delta/604800) == 1 ? ($future?'неделю':'Неделю') : $this->lexica(round($delta/604800),'неделю','недели','недель')).($future?'':' назад'); else return $delta % 86400 >= 82800 || $delta % 86400 <= 3600 ? ($future?'Через ':'').(round($delta/86400) == 1 ? ($future?'день':'День') : $this->lexica(round($delta/86400),'день','дня','дней')).($future?'':' назад') : (date('Ymd',$date) == date('Ymd',$future?(time()+86400):(time()-86400)) ? ($future?'Завтра':'Вчера') : (date('Ymd',$date) == date('Ymd',$future?(time()+172800):(time()-172800)) ? ($future?'Послезавтра':'Позавчера') : date('j',$date).' '.$months[date('n',$date)])).$time; }
            elseif(date('Y',$date) == date('Y')) return $delta % 2592000 >= 2505600 || ($delta % 2592000 <= 86400 && $delta >= 2505600) ? ($future?'Через ':'').(round($delta/2592000) == 1 ? ($future?'месяц':'Месяц') : $this->lexica(round($delta/2592000),'месяц','месяца','месяцев')).($future?'':' назад') : date('j',$date).' '.$months[date('n',$date)].$time;
            //else return $delta % 31536000 >= 28944000 || ($delta % 31536000 <= 2592000 && ($delta >= 28944000 && $delta >= 2592000)) ? ($future?'Через ':'').(round($delta/31536000) == 1 ? ($future?'год':'Год') : $this->lexica(round($delta/31536000),'год','года','лет')).($future?'':' назад') : date('j',$date).' '.$months[date('n',$date)].' '.date('Y',$date).' г.';
            else return date('j',$date).' '.$months[date('n',$date)].' '.date('Y',$date).' г. в '. date('H:i',$date);
        }
        else
        {
            $date = strtolower($date);
            if($date == 'прямо сейчас' || $date == 'только что') return time();
            elseif(preg_match('(^(через |)([0-9]+ |)(секунд[а-я]*|минут[а-я]*|час[а-я]*|день|дня|дней|недел[а-я]|месяц[а-я]*|год|года|лет)(| назад)$)i',$date,$temp))
            {
                $future = $temp[1] == 'через ';
                if($temp[3] == 'секунду' || $temp[3] == 'секунды' || $temp[3] == 'секунд') $x = 1;
                elseif($temp[3] == 'минуту' || $temp[3] == 'минуты' || $temp[3] == 'минут') $x = 60;
                elseif($temp[3] == 'час' || $temp[3] == 'часа' || $temp[3] == 'часов') $x = 3600;
                elseif($temp[3] == 'день' || $temp[3] == 'дня' || $temp[3] == 'дней') $x = 86400;
                elseif($temp[3] == 'неделю' || $temp[3] == 'недели' || $temp[3] == 'недель') $x = 604800;
                elseif($temp[3] == 'месяц' || $temp[3] == 'месяца' || $temp[3] == 'месяцев') $x = 2592000;
                elseif($temp[3] == 'год' || $temp[3] == 'года' || $temp[3] == 'лет') $x = 31536000;
                else return strtotime($date);
                return $temp[1] == 'через ' ? time()+(($temp[2]?$temp[2]:1)*$x) : time()-(($temp[2]?$temp[2]:1)*$x);
            }
            elseif(preg_match('(^(([0-9]+) (января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)( ([0-9]+) г.|)|сегодня|вчера|позавчера|завтра|послезавтра)( в ([0-9]+):([0-9]+)(:([0-9]+)|)|)$)',$date,$temp))
            {
                $months = array_flip($months);
                if($temp[1] == 'сегодня') return strtotime(date('Y-m-d')) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]);
                elseif($temp[1] == 'завтра') return strtotime(date('Y-m-d')) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]) + 86400;
                elseif($temp[1] == 'послезавтра') return strtotime(date('Y-m-d')) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]) + 172800;
                elseif($temp[1] == 'вчера') return strtotime(date('Y-m-d')) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]) - 86400;
                elseif($temp[1] == 'позавчера') return strtotime(date('Y-m-d')) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]) - 172800;
                elseif($temp[2] && array_key_exists($temp[3],$months)) return strtotime(($temp[5]?$temp[5]:date('Y')).'-'.$months[$temp['3']].'-'.$temp[2]) + (@$temp[7]*3600) + (@$temp[8]*60) + (@$temp[10]);
                else return strtotime($date);
            }
            else return strtotime($date);
        }
    }

}