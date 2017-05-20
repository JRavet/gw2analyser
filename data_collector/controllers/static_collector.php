<?php
function store_guild_upgrades()
{
    $guild_upgrades = json_decode(file_get_contents("https://api.guildwars2.com/v2/guild/upgrades?ids=all"));
    try
    {
        $conn = new PDO("mysql:host=localhost;dbname=Gw2Analyserv2", 'gw2adminv2', 'J0rDa1n');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM `guild_upgrade`;";
        $conn->exec($sql); //delete all current objectives
        foreach ($guild_upgrades as $upgrade)
        {
            echo "Storing upgrade id: " . $upgrade->id . "\n";
            $sql = "INSERT INTO `guild_upgrade` (id, name, description, build_time, icon)
                VALUES ($upgrade->id, \"$upgrade->name\", \"$upgrade->description\", $upgrade->build_time, '$upgrade->icon');";
            $conn->exec($sql);
        }
    }
    catch (Exception $e)
    {
        echo $e->getMessage();
    }
}

function store_log_codes()
{
    echo "Storing log codes\n";
    try
    {
        $conn = new PDO("mysql:host=localhost;dbname=Gw2Analyserv2", 'gw2adminv2', 'J0rDa1n');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM `log_codes`;";
        $conn->exec($sql); //delete all current log codes
        $sql = "INSERT INTO `log_codes` (id, type, message)
            VALUES 
                (-1, 'database error', 'A database error has occurred'),
                (0, 'note', ''),
                (1, 'info', 'Synchronizing to in-game clock for region'),
                (2, 'info', 'Synchronizing complete for region'),
                (3, 'info', 'New match-set detected'),
                (4, 'idle', 'Idling for time (microseconds)'),
                (5, 'process-start', 'Storing data for region'),
                (6, 'process-end', 'Finished storing data for region'),
                (7, 'info', 'Connection to database complete'),
                (8, 'process-start', 'Storing match_detail data'),
                (9, 'process-end', 'Finished storing match_detail data'),
                (10, 'process-start', 'Storing server_linkings data'),
                (11, 'process-end', 'Finished storing server_linkings data'),
                (12, 'process-start', 'Storing score data'),
                (13, 'process-end', 'Finished storing score data'),
                (14, 'process-start', 'Storing skirmish scores'),
                (15, 'process-end', 'Finished storing skirmish scores'),
                (16, 'process-start', 'Removing old logs'),
                (17, 'process-end', 'Finished removing old logs'),
                (18, 'file-deletion', 'Removing log file'),
                (19, 'folder-deletion', 'Removing log folder'),
                (20, 'file-removal prep', 'Preparing to remove files'),
                (21, 'storing data', ''),
                (22, 'stored data', ''),
                (500,'warning','A non-fatal error has occurred'),
                (501,'warning','Too much time elapsed storing data - resyncing'),
                (502,'warning','Invalid match-data detected. Attempting to restore data'),
                (503,'warning','Invalid match-data. Restoration failed'),
                (1000,'error','A fatal error has occurred'),
                (1001,'file error','An error involving files has occurred')
                ;";
        $conn->exec($sql);
    }
    catch(PDOException $e)
    {
        echo $sql . "\n" . $e->getMessage() . "\n";
    }
    $conn = null;
    return true;
}
//
function get_cardinal_direction($objective)
{
    if (preg_match("/The /",$objective->name))
    {
        return "N";
    }
    elseif (preg_match("/[a-z]*lake/",$objective->name))
    {
        return "SE";
    }
    elseif (preg_match("/water/",$objective->name))
    {
        return "SE";
    }
    elseif (preg_match("/-34/",$objective->id))
    {
        return "S";
    }
    elseif (preg_match("/-40/",$objective->id))
    {
        return "NE";
    }
    elseif (preg_match("/-51/",$objective->id))
    {
        return "NE";
    }
    elseif (preg_match("/-38/",$objective->id))
    {
        return "NW";
    }
    elseif (preg_match("/52/",$objective->id))
    {
        return "NW";
    }
    elseif (preg_match("/vale/",$objective->name))
    {
        return "SW";
    }
    elseif (preg_match("/briar/",$objective->name))
    {
        return "SW";
    }
    elseif (preg_match("/Necropolis/",$objective->name))
    {
        return "NE";
    }
    else if (preg_match("/'s Refuge/",$objective->name))
    {
        return "NE";
    }
    else if (preg_match("/Palace/",$objective->name))
    {
        return "E";
    }
    else if (preg_match("/Farmstead/",$objective->name))
    {
        return "SE";
    }
    else if (preg_match("/Depot/",$objective->name))
    {
        return "SE";
    }
    else if (preg_match("/Well/",$objective->name))
    {
        return "S";
    }
    else if (preg_match("/Outpost/",$objective->name))
    {
        return "SW";
    }
    else if (preg_match("/Encampment/",$objective->name))
    {
        return "SW";
    }
    else if (preg_match("/Undercroft/",$objective->name))
    {
        return "W";
    }
    else if (preg_match("/Hideaway/",$objective->name))
    {
        return "NW";
    }
    else if (preg_match("/Academy/",$objective->name))
    {
        return "NW";
    }
    else if (preg_match("/Lab/",$objective->name))
    {
        return "N";
    }
    else if (preg_match("/Rampart/",$objective->name))
    {
        return "N";
    }
    else
    {
        return "";
    }
}
//
function get_base_ppt($type)
{ //ppt = tier*0.5*base+base
    if ($type == "Camp")
    {
        return 2;
    }
    elseif($type == "Tower")
    {
        return 4;
    }
    elseif($type == "Keep")
    {
        return 8;
    }
    elseif($type == "Castle")
    {
        return 12;
    }
}
//
function store_supply_routes()
{
    echo "Storing supply routes\n";
    try
    {
        $conn = new PDO("mysql:host=localhost;dbname=Gw2Analyserv2", 'gw2adminv2', 'J0rDa1n');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("DELETE FROM `supply_routes`;");
        $sql = "INSERT INTO `supply_routes` (from_obj, to_obj, estimated_travel_time)
        VALUES
        #BEGIN GREEN BL
            #BEGIN S CAMP
                ('95-34','95-33',5), #TO BAY
                ('95-34','95-32',5), #TO HILLS
                ('95-34','95-35',5), #TO SWT
                ('95-34','95-36',5), #TO SET
            #END S CAMP
            #BEGIN SE CAMP
                ('95-50','95-32',5), #TO HILLS
                ('95-50','95-36',5), #TO SET
            #END SE CAMP
            #BEGIN SW CAMP
                ('95-53','95-33',5), #TO BAY
                ('95-53','95-35',5), #TO SWT
            #END SW CAMP
            #BEGIN NW CAMP
                ('95-52','95-37',7), #TO GARRI
                ('95-52','95-33',7), #TO BAY
            #END NW CAMP
            #BEGIN NE CAMP
                ('95-51','95-37',7), #TO GARRI
                ('95-51','95-32',7), #TO HILLS
            #END NE CAMP
            #BEGIN N CAMP
                ('95-39','95-38',5), #TO NWT
                ('95-39','95-40',5), #TO NET
                ('95-39','95-37',7), #TO GARRI
            #END N CAMP
        #END GREEN BL
        #BEGIN BLUE BL
            #BEGIN S CAMP
                ('96-34','96-33',5), #TO BAY
                ('96-34','96-32',5), #TO HILLS
                ('96-34','96-35',5), #TO SWT
                ('96-34','96-36',5), #TO SET
            #END S CAMP
            #BEGIN SE CAMP
                ('96-50','96-32',3), #TO HILLS
                ('96-50','96-36',3), #TO SET
            #END SE CAMP
            #BEGIN SW CAMP
                ('96-53','96-33',3), #TO BAY
                ('96-53','96-35',3), #TO SWT
            #END SW CAMP
            #BEGIN NW CAMP
                ('96-52','96-37',4), #TO GARRI
                ('96-52','96-33',2), #TO BAY
            #END NW CAMP
            #BEGIN NE CAMP
                ('96-51','96-37',5), #TO GARRI
                ('96-51','96-32',2), #TO HILLS
            #END NE CAMP
            #BEGIN N CAMP
                ('96-39','96-38',2.5), #TO NWT
                ('96-39','96-40',3.5), #TO NET
                ('96-39','96-37',6), #TO GARRI
            #END N CAMP
        #END BLUE BL
        #BEGIN RED BL
            #BEGIN N CAMP
                ('1099-99','1099-104',4.25), #TO NET
                ('1099-99','1099-102',3), #TO NWT
                ('1099-99','1099-113',1), #TO EARTH
            #END N CAMP
            #BEGIN NE CAMP
                ('1099-109','1099-114',2), #TO AIR
                ('1099-109','1099-104',3), #TO NET
                ('1099-109','1099-113',4), #TO EARTH
            #END NE CAMP
            #BEGIN NW CAMP
                ('1099-115','1099-106',2), #TO FIRE
                ('1099-115','1099-102',3), #TO NWT
                ('1099-115','1099-113',4.5), #TO EARTH
            #END NW CAMP
            #BEGIN SW CAMP
                ('1099-101','1099-110',3), #TO SWT
                ('1099-101','1099-106',2.25), #TO FIRE
            #END SW CAMP
            #BEGIN SE CAMP
                ('1099-100','1099-105',2.25), #TO SET
                ('1099-100','1099-114',3), #TO AIR
            #END SE CAMP
            #BEGIN S CAMP
                ('1099-116','1099-110',2.5), #TO SWT
                ('1099-116','1099-105',3), #TO SET
            #END S CAMP
        #END RED BL
        #BEGIN EB
            #BEGIN ROGUES QUARRY
                ('38-10','38-3',5), #TO GREEN KEEP
                ('38-10','38-11',5), #TO ALDONS
                ('38-10','38-12',3.5), #TO WILDCREEK
                ('38-10','38-9',5), #TO SMC
            #END ROGUES QUARRY
            #BEGIN GOLANTA
                ('38-4','38-3',5), #TO GREEN KEEP
                ('38-4','38-14',5), #TO KLOVAN
                ('38-4','38-13',5), #TO JERRIS
                ('38-4','38-9',5), #TO SMC
            #END GOLANTA
            #BEGIN SPELDANS CLEARCUT
                ('38-6','38-1',4), #TO RED KEEP
                ('38-6','38-18',5), #TO ANZ
                ('38-6','38-17',3.5), #TO MENDONS
                ('38-6','38-9',5), #TO SMC
            #END SPELDANS CLEARCUT
            #BEGIN PANGLOSS RISE
                ('38-5','38-1',3.5), #TO RED KEEP
                ('38-5','38-20',3), #TO VELOKA
                ('38-5','38-19',3), #TO OGREWATCH
                ('38-5','38-9',6), #TO SMC
            #END PANGLOSS RISE
            #BEGIN UMBERGLADE WOODS
                ('38-8','38-2',5), #TO BLUE KEEP
                ('38-8','38-21',5), #TO DURIOUS
                ('38-8','38-22',4), #TO BRAVOST
                ('38-8','38-9',5), #TO SMC
            #END UMBERGLADE WOODS
            #BEGIN DANELON PASSAGE 
                ('38-7','38-2',4), #TO BLUE KEEP
                ('38-7','38-15',3.25), #TO LANGOR
                ('38-7','38-16',2.75), #TO QUENTIN
                ('38-7','38-9',5) #TO SMC
            #END DANELON PASSAGE
        #END EB
        ;";
        $conn->exec($sql);
        $conn = null;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage();
    }
}
//
function store_objectives()
{
   echo "Storing objectives\n";
   $objectives = json_decode(file_get_contents("https://api.guildwars2.com/v2/wvw/objectives?ids=all"));
   try
   {
        $conn = new PDO("mysql:host=localhost;dbname=Gw2Analyserv2", 'gw2adminv2', 'J0rDa1n');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM `objectives`;";
        $conn->exec($sql); //delete all current objectives
        foreach ($objectives as $objective)
        {
            $direction = get_cardinal_direction($objective);
            $ppt = get_base_ppt($objective->type);
            $coordX = 0;
            $coordY = 0;
            $coordZ = 0;
            $label_coordX = 0;
            $label_coordY = 0;
            //reset the stored coordinates; prevents coordinateless objectives from receiving old data
            if (isset($objective->coord))
            {
                $coordX = $objective->coord[0];
                $coordY = $objective->coord[1];
                $coordZ = $objective->coord[2];
            }
            if (isset($objective->label_coord))
            {
                $label_coordX = $objective->label_coord[0];
                $label_coordY = $objective->label_coord[1];
            }
            if (isset($objective->marker))
            {
                $marker = $objective->marker;
            }
            $chatLink = $objective->chat_link;
            $sql = "INSERT INTO `objectives` (obj_id, name, ppt_base, type, sector_id, map_id, map_type,
                coordX, coordY, coordZ, label_coordX, label_coordY, marker, compass_direction, chat_link)
                VALUES ('$objective->id', \"$objective->name\", '$ppt', '$objective->type', '$objective->sector_id',
                '$objective->map_id', '$objective->map_type', '$coordX', '$coordY', '$coordZ', '$label_coordX', '$label_coordY',
                '$marker', '$direction', \"$chatLink\");";
            $conn->exec($sql);
        }
    }
    catch(PDOException $e)
    {
        echo $sql . "\n" . $e->getMessage() . "\n";
        $conn = null;
        return false;
    }
    $conn = null;
    return true;
}
//
function get_server_abbreviation($server_name)
{
    if ($server_name == "Anvil Rock")
    {
        return "AR";
    }
    else if ($server_name == "Borlis Pass")
    {
        return "BP";
    }
    else if ($server_name == "Yak's Bend")
    {
        return "YB";
    }
    else if ($server_name == "Henge of Denravi")
    {
        return "HoD";
    }
    else if ($server_name == "Maguuma")
    {
        return "Mag";
    }
    else if ($server_name == "Sorrow's Furnace")
    {
        return "SF";
    }
    else if ($server_name == "Gate of Madness")
    {
        return "GoM";
    }
    else if ($server_name == "Jade Quarry")
    {
        return "JQ";
    }
    else if ($server_name == "Fort Aspenwood")
    {
        return "FA";
    }
    else if ($server_name == "Ehmry Bay")
    {
        return "Ebay";
    }
    else if ($server_name == "Stormbluff Isle")
    {
        return "SBI";
    }
    else if ($server_name == "Darkhaven")
    {
        return "DH";
    }
    else if ($server_name == "Sanctum of Rall")
    {
        return "SoR";
    }
    else if ($server_name == "Crystal Desert")
    {
        return "CD";
    }
    else if ($server_name == "Isle of Janthir")
    {
        return "IoJ";
    }
    else if ($server_name == "Sea of Sorrows")
    {
        return "SoS";
    }
    else if ($server_name == "Tarnished Coast")
    {
        return "TC";
    }
    else if ($server_name == "Northern Shiverpeaks")
    {
        return "NSP";
    }
    else if ($server_name == "Blackgate")
    {
        return "BG";
    }
    else if ($server_name == "Ferguson's Crossing")
    {
        return "FC";
    }
    else if ($server_name == "Dragonbrand")
    {
        return "DB";
    }
    else if ($server_name == "Devona's Rest")
    {
        return "DR";
    }
    else if ($server_name == "Eredon Terrace")
    {
        return "ET";
    }
    else if ($server_name == "Kaineng")
    {
        return "Kg";
    }
    else
    {
        return ""; //no shorthand name; includes Kaineng and EU servers
    }
}
//
function store_servers()
{
    echo "Storing servers\n";
    $servers = json_decode(file_get_contents("https://api.guildwars2.com/v2/worlds?ids=all"));
    try
    {
        $conn = new PDO("mysql:host=localhost;dbname=Gw2Analyserv2", 'gw2adminv2', 'J0rDa1n');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM `server_info`;";
        $conn->exec($sql); //delete all current servers
        foreach ($servers as $server)
        {
            $abbreviation = get_server_abbreviation($server->name);
            $sql = "INSERT INTO `server_info` (server_id, name, abbreviation)
            VALUES ($server->id, \"$server->name\", '$abbreviation');";
            $conn->exec($sql);
        }
        //
        $sql = "INSERT INTO `server_info` (server_id, name, abbreviation) VALUES(0,'Neutral',\"\");";
        $conn->exec($sql); //neutral server
        $sql = "INSERT INTO `guild` (guild_id, emblem_last_updated, name, tag) VALUES('','','','');";
        $conn->exec($sql); //empty guild, for unclaimed objectives
    }
    catch(PDOException $e)
    {
        echo $sql . "\n" . $e->getMessage() . "\n";
        $conn = null;
        return false;
    }
    $conn = null;
    return true;
}
if (sizeof($argv) == 1 || $argv[1] == "-a" || $argv[1] == "-o")
{
    store_objectives();
}
if (sizeof($argv) == 1 || $argv[1] == "-a" || $argv[1] == "-s")
{
    store_servers();
}
if (sizeof($argv) == 1 || $argv[1] == "-a" || $argv[1] == "-c")
{
    store_log_codes();
}
if (sizeof($argv) == 1 || $argv[1] == "-a" || $argv[1] == "-r")
{
    store_supply_routes();
}
if (sizeof($argv) == 1 || $argv[1] == "-a" || $argv[1] == "-g")
{
    store_guild_upgrades();
}
?>