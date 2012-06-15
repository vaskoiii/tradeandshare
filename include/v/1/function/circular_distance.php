<?
/*
Copyright 2003-2012 John Vasko III

This file is part of Trade and Share.

Trade and Share is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Trade and Share is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Trade and Share.  If not, see <http://www.gnu.org/licenses/>.
*/

# Contents/Description: All the basics for calculating circular distance. See:
# http://mathforum.org/library/drmath/view/66987.html
# http://mathforum.org/library/drmath/view/51804.html

# Formulas Used:
# dlon = lon2 - lon1
# dlat = lat2 - lat1
# a = (sin(dlat/2))^2 + cos(lat1) * cos(lat2) * (sin(dlon/2))^2
# c = 2 * atan2(sqrt(a), sqrt(1-a))
# d = R * c

# max_lon = lon1 + arcsin(sin(D/R)/cos(lat1))
# min_lon = lon1 - arcsin(sin(D/R)/cos(lat1))

# max_lat = lat1 + (180/pi)(D/R)
# min_lat = lat1 - (180/pi)(D/R)

function get_latitude_range_in_miles($distance) {
	return $distance/69.172;
}

function get_longitude_range_in_miles($distance, $latitude ) {
	return abs($distance/(cos($latitude) * 69.172));
}

function get_distance_in_miles($latitude1, $longitude1, $latitude2, $longitude2) {
	$latitude1 = deg2rad($latitude1);
	$longitude1 = deg2rad($longitude1);
	$latitude2 = deg2rad($latitude2);
	$longitude2 = deg2rad($longitude2);

	$delta_latitude = $latitude2 - $latitude1;
	$delta_longitude = $longitude2 - $longitude1;

	# Haversine Formula - Great for distance approximations on the globe with reguard to smaller distances.
	$haversine_formula = pow(sin($delta_latitude/2.0),2) + cos($latitude1) * cos($latitude2) * pow(sin($delta_longitude/2.0),2);

	# Return the Great Circle distance
	return (3956 * 2 * atan2(sqrt($haversine_formula), sqrt(1 - $haversine_formula)));
}

function get_kilometers_from_miles($miles) {
	$kilometers_per_mile = 1.609344;
	return ($kilometers_per_mile * $miles);
}
