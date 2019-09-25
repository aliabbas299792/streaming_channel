This is a project using Ratchet and the ReactPHP Event Loop to make a pseudo live stream, so all connected users will see the same footage at the same time.

In my own project it was used with a chat implementation on the left, but due to bandwith restrictions I cancelled that.

If you're interested I'll have to alert that it doesn't actually have a backend at this point, however, it's not too hard to get it to work.

1) Go to include/db.info.php and update to match your MySQL details
 --> Do the same in video-server/bin/video-server.php
 2) Create a database named `erewhon` and add the following

```
CREATE TABLE `stream_schedule` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `list_vids` varchar(40) COLLATE utf8mb4_bin NOT NULL
)

INSERT INTO `stream_schedule` (`id`, `time`, `list_vids`) VALUES (1, '2018-07-21 18:54:00', '1 2 10');

-- --------------------------------------------------------

CREATE TABLE `stream_videos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `length` float NOT NULL,
)
```

3) Insert whatever videos you want in to the `stream_videos` table, with the exact time (use JS or something to retrieve it)
4) Make a stream in `stream_schedule`, set what time you want it to be at, and in the `list_vids` put the ids of the videos you want to play, and ensure they are space delimited
5) Then go to video-server/bin and through command line do `php video-server.php` and it will be running and serving the content
---> If you want it to work in the background and are on linux (ubuntu), do `nohup php video-server.php &`
