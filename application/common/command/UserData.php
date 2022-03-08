<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/9/23
 * Time: 9:37
 */

namespace app\common\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class UserData extends Command
{
    protected function configure()
    {
        $this->setName('UserData')->setDescription('总数用户，新增用户数，团员团干，少先队');
    }

    protected function execute(Input $input, Output $output)
    {

        $today = strtotime(date("Y-m-d"),time());
        $dateStart =  date("Y-m-d H:i:s",$today);
        $end = $today+60*60*24;
        $dateEnd =  date("Y-m-d H:i:s",$end);

        $userSumData = Db::name('users')->where('is_delete','=',0)->count();
      //  $userTodayData = Db::name('users')->whereBetweenTime('create_time',$dateStart,$dateEnd)->count();

      //  $memberDataSum = Db::name('league_member')->distinct(true)->field('userid')->where(['is_delete'=>0,'identity'=>0])->count();

      $sql = "SELECT
	sum( ifnull( 团干, 0 ) ) AS 团干,
	sum( ifnull( 已回家团干, 0 ) ) AS 已回家团干,
	sum(ifnull(ty.团员总数,0)) as 团员总数,
	sum(ifnull(ty.已回家团员,0)) as 已回家团员,
	sum(ifnull(ty.待审核团员,0)) as 待审核团员
	
FROM
	hbgqt_organization org
	
LEFT JOIN (
	SELECT
		leagueid,
		sum( CASE WHEN identity = 0 THEN 1 ELSE 0 END ) 团员总数,
		sum( CASE WHEN identity = 0 AND u.mobile IS NOT NULL THEN 1 ELSE 0 END ) 已回家团员,
		sum( CASE WHEN identity = 0 AND ( league_status = 95 ) THEN 1 ELSE 0 END ) 待审核团员,
		sum( CASE WHEN identity = 1 THEN 1 ELSE 0 END ) 联系青年 
	FROM
		hbgqt_league_member
		INNER JOIN hbgqt_organization o ON leagueid = o.id
		INNER JOIN hbgqt_users u ON userid = u.id 
	WHERE
		hbgqt_league_member.is_delete = 0 
		AND o.organize_type = 131 
		AND o.is_deleted = 0 
		AND o.dissolution = 0 
		AND u.is_delete = 0 
		AND u.idcard IS NOT NULL 
		AND u.idcard != '' 
	GROUP BY
		leagueid 
	) ty ON org.id = ty.leagueid
	
	
	LEFT JOIN (
	SELECT
		hbgqt_league_cadre.leagueid,
		count( * ) 团干,
		sum( CASE WHEN m.userid IS NULL THEN 0 ELSE 1 END ) AS 已回家团干 
	FROM
		hbgqt_league_cadre
		LEFT JOIN (
		SELECT
			userid 
		FROM
			hbgqt_league_member
			INNER JOIN hbgqt_organization o ON leagueid = o.id
			INNER JOIN hbgqt_users u ON userid = u.id 
		WHERE
			hbgqt_league_member.is_delete = 0 
			AND hbgqt_league_member.identity = 0 
			AND o.organize_type = 131 
			AND o.is_deleted = 0 
			AND o.dissolution = 0 
			AND userid IS NOT NULL 
			AND u.is_delete = 0 
			AND u.mobile IS NOT NULL 
			AND u.idcard IS NOT NULL 
			AND u.idcard !=  ''
			) m on hbgqt_league_cadre.userid is not null and hbgqt_league_cadre.userid=m.userid
			inner join hbgqt_organization o on hbgqt_league_cadre.leagueid=o.id
			where is_on != 120
			and o.is_deleted=0 and o.dissolution=0
			group by hbgqt_league_cadre.leagueid ) tg on org.id=tg.leagueid
			
			
			
	where org.is_deleted=0 and org.dissolution=0
	and (org.code like '000.017%')";


       $resultTuanyuan  = Db::query($sql);


        $memberDataSum = $resultTuanyuan[0]['团员总数'];
//        $memberDataSum = Db::name('league_member')
//            ->alias('lm')
//            ->leftJoin('hbgqt_organization o','lm.leagueid=o.id')
//            ->leftJoin('hbgqt_users u','lm.userid = u.id')
//            ->field('userid')
//            ->where(['lm.is_delete'=>0,'lm.identity'=>0,'o.is_deleted'=>0,'o.dissolution'=>0,'o.organize_type'=>131,'u.is_delete'=>0])
//            ->where('o.code','like','000.017%')
//            ->where('u.idcard','not null')
//            ->count();
      //  $memberDataSumSuccess = Db::name('league_member')->distinct(true)->field('userid')->where(['is_delete'=>0,'identity'=>0,'league_status'=>92])->count();


        $memberDataSumSuccess = $resultTuanyuan[0]['已回家团员'] - $resultTuanyuan[0]['待审核团员'];
//        $memberDataSumSuccess = Db::name('league_member')
//            ->alias('lm')
//            ->leftJoin('hbgqt_organization o','lm.leagueid=o.id')
//            ->leftJoin('hbgqt_users u','lm.userid = u.id')
//            ->field('userid')
//            ->where(['lm.is_delete'=>0,'lm.identity'=>0,'o.is_deleted'=>0,'o.dissolution'=>0,'o.organize_type'=>131,'u.is_delete'=>0,'lm.go_home_status'=>3])
//            ->where('o.code','like','000.017%')
//            ->where('u.idcard','not null')
//            ->count();

    //    $memberData = Db::name('league_member')->distinct(true)->field('userid')->where(['is_delete'=>0,'identity'=>0,'league_status'=>95])->whereBetweenTime('create_time',$today,$end)->count();
    //    $leagueCadresData = Db::name('league_cadre')->field('userid')->whereBetweenTime('create_time',$today,$end)->count();
    //    $leagueCadresDataSum = Db::name('league_cadre')->field('userid')->count();

        $leagueCadresDataSum =  $resultTuanyuan[0]['已回家团干'];

//        $leagueCadresDataSum = Db::name('league_cadre')
//            ->alias('lm')
//            ->leftJoin('hbgqt_organization o','lm.leagueid=o.id')
//            ->leftJoin('hbgqt_users u','lm.userid = u.id')
//            ->field('userid')
//            ->where(['o.is_deleted'=>0,'o.dissolution'=>0,'o.organize_type'=>131,'u.is_delete'=>0])
//            ->where('o.code','like','000.017%')
//            ->where('u.idcard','not null')
//            ->count();

        //少先队

//        $sqloo = "SELECT
//	sum( ifnull( ts.少先队员, 0 ) ) AS 少先队员,
//	sum( ifnull( ts.已回家队员, 0 ) ) AS 已回家队员,
//	sum( ifnull( 辅导员, 0 ) ) AS 辅导员,
//	sum( ifnull( 已回家辅导员, 0 ) ) AS 已回家辅导员
//FROM
//	hbgqt_organization org
//	LEFT JOIN (
//	SELECT
//		school,
//		count( * ) AS 少先队员,
//		sum( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END ) AS 已回家队员
//	FROM
//		hbgqt_children
//		INNER JOIN hbgqt_organization o ON school = o.id
//	WHERE
//		hbgqt_children.is_delete = 0
//		AND o.organize_type = 90
//		AND o.is_deleted = 0
//		AND o.dissolution = 0
//	GROUP BY
//		school
//	) ts ON org.id = ts.school
//	LEFT JOIN (
//	SELECT
//		organize_id,
//		count( * ) 辅导员
//	FROM
//		hbgqt_instructor
//		INNER JOIN hbgqt_organization o ON organize_id = o.id
//	WHERE
//		hbgqt_instructor.is_delete = 0
//		AND work_status != 120
//		AND o.is_deleted = 0
//		AND o.dissolution = 0
//	GROUP BY
//		organize_id
//	) tf ON org.id = tf.organize_id
//	LEFT JOIN (
//	SELECT
//		organize_id,
//		sum( CASE WHEN u.mobile IS NULL THEN 0 ELSE 1 END ) AS 已回家辅导员
//	FROM
//		hbgqt_instructor
//		LEFT JOIN hbgqt_users u ON hbgqt_instructor.idcard = u.idcard
//		INNER JOIN hbgqt_organization o ON organize_id = o.id
//	WHERE
//		hbgqt_instructor.is_delete = 0
//		AND work_status != 120
//		AND u.is_delete = 0
//		AND u.idcard IS NOT NULL
//		AND u.idcard != ''
//		AND o.is_deleted = 0
//		AND o.dissolution = 0
//	GROUP BY
//		organize_id
//	) tf1 ON org.id = tf1.organize_id
//WHERE
//	org.is_deleted = 0
//	AND org.dissolution = 0
//	AND ( org.CODE LIKE '000.018%' )";


//        $resultchild  = Db::query($sqloo);

        $childCount = Db::name('children')->where(['is_delete'=>0])->count();
  //      $childCount = $resultchild[0]['少先队员'];

        $childGoHome = Db::name('children')->where(['is_delete'=>0,'status'=>1])->count();

   //     $childGoHome = $resultchild[0]['已回家队员'];
        $childInstructor = Db::name('instructor')->where([['is_delete','=',0],['work_status','<>',120]])->count();

    //    $childInstructor = $resultchild[0]['辅导员'];
        $childInstructorGoHome = Db::name('instructor')->where([['is_delete','=',0],['user_id','<>',0],['work_status','<>',120]])->count();
    //    $childInstructorGoHome = $resultchild[0]['已回家辅导员'];
     //   $message = '平台注册用户总数'.$userSumData.'人 ,其中以团员身份注册数'.$memberDataSum.'人,经团支部书记审核通过的团员数'.$memberDataSumSuccess.'人 ,团干部回家数为'.$leagueCadresDataSum.'人;今天新增用户 '.$userTodayData.'人, 团员'.$memberData.'人,团干'.$leagueCadresData.'人';
        $message = '平台注册用户总数'.$userSumData.',其中以团员身份注册数'.$memberDataSum.',经团支部书记审核通过的团员数'.$memberDataSumSuccess.',团干部回家数为'.$leagueCadresDataSum.' 。“红领巾”模块后台录入少先队员数 '.$childCount.', 少先队员“回家”数'.$childGoHome.',后台录入少先队辅导员数'.$childInstructor.'，少先队辅导员“回家”数'.$childInstructorGoHome;
        $redis = new \Redis();
        $redis ->connect(config('queue.host'),config('queue.port'));
        $redis ->auth(config('queue.password'));
        $redis ->select(config('queue.select'));

//        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'17334390057','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'15697586693','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'18617869296','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'13925026678','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'18003390010','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'18803111128','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'13903119889','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'13290565797','message'=>$message,'time'=>time()+300]));
        $redis->lPush('yiWangSendCode',json_encode(['mobile'=>'15713380183','message'=>$message,'time'=>time()+300]));

    }
}