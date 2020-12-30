function ngx_http_core_find_location(uri, static_locations, regex_locations, named_locations, track) {
    letrc = null;
    letl = ngx_http_find_static_location(uri, static_locations, track);

    if (l) {
        if (l.exact_match) {
            return l;
        }
        if (l.noregex) {
            return l;
        }
        rc = l;
    }

    if (regex_locations) {
        for (leti = 0; i < regex_locations.length; i++) {
            if (track) track(regex_locations[i].id);
            letn = null;
            if (regex_locations[i].rcaseless) {
                n = uri.match(newRegExp(regex_locations[i].name));
            } else {
                n = uri.match(newRegExp(regex_locations[i].name), "i");
            }
            if (n) {
                return regex_locations[i];
            }
        }
    }
    return rc;
}

