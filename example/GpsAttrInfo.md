# Exif: GPS Attribute Information

|Field Name|Type|Count|Values|Default|Separator|
|:---|:---|---:|:---|:---:|:---:|
|GPSVersion|BYTE|4|---|---|.|
|GPSVersionID|BYTE|4|---|2.4.0.0|.|
|GPSLatitudeRef|ASCII|2|* N: North latitude<br />* S: South latitude|None|---|
|GPSLatitude|RATIONAL|3|---|None|---|
|GPSLongitudeRef|ASCII|2|* E: East longitude<br />* W: West longitude|None|---|
|GPSLongitude|ASCII|3|---|None|---|
|GPSAltitudeRef|BYTE|1|* 0: Positive ellipsoidal height (at or above ellipsoidal surface)<br />* 1: Negative ellipsoid height (below ellipsoidal surface)<br />* 2: Positive sea level value (at or above sea level reference)<br />* 3: Negative sea level value (below sea level reference)|0||
|GPSAltitude|RATIONAL|1|---|None|---|
|GPStimeStamp|RATIONAL|3|---|None|---|
|GPSSatellites|ASCII|0|---|None|---|
|GPSStatus|ASCII|2|* A: Measurement in progress<br />* V: Measurement interrupted|None|---|
|GPSMeasureMode|ASCII|2|* 2: 2-dimensional measurement<br />* 3: 3-dimensional measurement|None|---|
|GPSDOP|RATIONAL|1|---|None|---|
|GPSSpeedRef|ASCII|2|* K: k/m<br />* M: mph<br />* n: knots|K|---|
|GPSSpeed|RATIONAL|1|---|None|---|
|GPSTrackRef|ASCII|2|* T: true bearing<br />* M: magnetic bearing|T|---|
|GPSTrack|RATIONAL|1|---|None|---|
|GPSImgDirectionRef|ASCII|2|* T: true bearing<br />* M: magnetic bearing|T|---|
|GPSImgDirection|RATIONAL|1|---|None|---|
|GPSMapDatum|ASCII|0|---|None|---|
|GPSDestLatitudeRef|ASCII|2|* N: North<br />* S: South|None|---|
|GPSDestLatitude|RATIONAL|3|---|None|---|
|GPSDestLongitudeRef|ASCII|2|* E: East<br />* W: West|None|---|
|GPSDestLongitude|RATIONAL|3|---|None|---|
|GPSDestBearingRef|ASCII|2|* T: true bearing<br />* M: magnetic bearing|T|---|
|GPSDestBearing|RATIONAL|1|---|None|---|
|GPSDestDistanceRef|ASCII|2|* K: kilometers<br />* M: miles<br />* N: nautical miles|K|---|
|GPSDestDistance|RATIONAL|1|---|None|---|
|GPSProcessingMethod|UNDEFINED|0|* GPS: GPS [GPSMeaMeasureMode: 2 or 3]<br />* QZSS: Quasi-Zenith Satellite System [GPSMeaMeasureMode: 2 or 3]<br />* GALILEO: Galileo System [GPSMeaMeasureMode: 2 or 3]<br />* GLONASS: Global Navigation Satellite System [GPSMeaMeasureMode: 2 or 3]<br />* BEIDOU: Beidou Satellite Positioning System [GPSMeaMeasureMode: 2 or 3]<br />* NAVID: Navigation Indian Constellation System [GPSMeaMeasureMode: 2 or 3]<br />* CELLID: Mobile Phone Base Stations [GPSMeaMeasureMode: 2 (generally)]<br />* WLAN: Wireless LAN [GPSMeaMeasureMode: 2 (generally)]<br />* MANUAL: Manual input [GPSMeaMeasureMode: (not recorded)]|None|---|
|GPSAreaInformation|UNDEFINED|0|---|None|---|
|GPSDateStamp|ASCII|11|---|None|---|
|GPSDifferential|SHORT|1|* 0: Stand Alone Positioning<br />* 1: Differential GPS|None|---|
|GPSHPositioningError|RATIONAL|1|---|None|---|
