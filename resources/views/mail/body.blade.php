<html>
    <body>
        <p>{{ $user->name }}さん、こんにちわ</p>
        <p>{{ $reservation->nhk_title }}の<br>
            開始時間：{{ $reservation->start_time }}までもうすぐです。<br>
            お見逃しなく！
        </p>
    </body>
</html>
