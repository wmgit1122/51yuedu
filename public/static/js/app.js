var LocalCache = {
    NULL_VALUE: '<null>',
    set: function(key, value, options) {
        if (!key) throw new Error('key is required');
        if (value === null || value === undefined) {
            value = LocalCache.NULL_VALUE;
        }
        if (value && typeof value === 'object') {
            value = JSON.stringify(value);
        }
        window.localStorage.setItem(key, value);
        if (options && options.ttl) {
            window.localStorage.setItem(key + '_ets', Math.floor(new Date().getTime() / 1000) + options.ttl);
        } else if (options && options.etag) {
            window.localStorage.setItem(key + '_etag', options.etag);
        }
    },
    get: function(key, options) {
        var entry = LocalCache.getEntry(key, options);
        return entry ? entry.value: null;
    },
    getObject: function(key, options) {
        var value = LocalCache.get(key, options);
        return value ? JSON.parse(value) : null;
    },
    getEntry: function(key, options) {
        if (options && options.etag) {
            var oldEtag = window.localStorage.getItem(key + '_etag');
            if (oldEtag !== options.etag) {
                LocalCache.remove(key);
                return null;
            }
        }
        var expireTime = window.localStorage.getItem(key + '_ets');
        if (expireTime && expireTime < new Date().getTime() / 1000) {
            LocalCache.remove(key);
            return null;
        }
        var value = window.localStorage.getItem(key);
        var entry = null;
        if (value) {
            entry = {
                value: value === LocalCache.NULL_VALUE ? null: value,
                readAsObject: function() {
                    return this.value ? JSON.parse(this.value) : null;
                }
            }
        }
        return entry;
    },
    remove: function(key) {
        window.localStorage.removeItem(key);
        window.localStorage.removeItem(key + '_ets');
        window.localStorage.removeItem(key + '_etag');
    }
};
Handlebars.registerHelper('truncate',
    function(text, len) {
        if (!text || text.length <= len) {
            return text;
        }
        return text.substr(0, len) + '...';
    });
Handlebars.registerHelper('timestamp',
    function(ts, format) {
        return moment(ts * 1000).format(_.isString(format) ? format: 'YYYY-MM-DD HH:mm:ss');
    });
Handlebars.registerHelper('if_same',
    function(val1, val2, options) {
        return val1 === val2 ? options.fn(this) : options.inverse(this);
    });
Handlebars.registerHelper('if_equal',
    function(val1, val2, options) {
        return val1 == val2 ? options.fn(this) : options.inverse(this);
    });
function getMemberToken() {
    return Cookies.get(getChannelScopeKey('member_token'));
}
function setMemberToken(token, expiresIn) {
    if (!token) throw new Error('token is required');
    Cookies.set(getChannelScopeKey('member_token'), token, {
        expires: expiresIn
    });
}
function isLoggedIn() {
    return !! getMemberToken();
}
function getChannelScopeKey(key) {
    var domainMode = window.__channelDomainMode;
    if (domainMode === 'union_domain') {
        if (!window.__channelId) {
            throw new Error('Requires __channelId');
        }
        return 'c' + window.__channelId + '_' + key;
    }
    return key;
}
function getMpInfo(channelId) {
    var defer = $.Deferred();
    var cacheKey = 'mp_info:' + channelId;
    var channel = LocalCache.getObject(cacheKey);
    if (channel) {
        defer.resolve(channel);
    } else {
        $.get('/api/channel/mp_info', {
                channel_id: channelId
            },
            function(resp) {
                LocalCache.set(cacheKey, resp.data, {
                    ttl: 3600
                });
                defer.resolve(resp.data);
            });
    }
    return defer.promise();
}
function getCurrentMemberShortProfile() {
    var defer = $.Deferred();
    var token = getMemberToken();
    if (!token) {
        defer.reject(new Error('登陆超时'));
    } else {
        var cacheKey = getChannelScopeKey('profile');
        var profile = LocalCache.getObject(cacheKey, {
            etag: token
        });
        if (profile) {
            defer.resolve(profile);
        } else {
            $.get('/api/my/profile',
                function(result) {
                    LocalCache.set(cacheKey, result.data, {
                        etag: token
                    });
                    defer.resolve(result.data);
                });
        }
    }
    return defer.promise();
}
function qs2hash(querystring) {
    return _.chain(querystring).replace('?', '').split('&').map(_.ary(_.partial(_.split, _, '='), 1)).fromPairs().value();
}
var ReadlogCache = {
    all: function() {
        return this._deserialize();
    },
    last: function() {
        var all = this.all();
        return all.length > 0 ? all[0] : null;
    },
    find: function(novelId) {
        return _.find(this.all(), {
            nid: parseInt(novelId)
        });
    },
    insert: function(readlog) {
        var readlogs = this.all();
        var existing = _.find(readlogs, {
            nid: readlog.nid
        });
        if (existing && existing.idx >= readlog.idx) {
            return;
        }
        _.remove(readlogs, {
            nid: readlog.nid
        });
        readlogs.unshift(readlog);
        this._serialize(readlogs);
    },
    remove: function(nid) {
        var readlogs = this.all();
        _.remove(readlogs, {
            'nid': nid
        });
        this._serialize(readlogs);
    },
    merge: function(readlogs) {
        var self = this;
        _.each(readlogs,
            function(readlog) {
                self.insert(readlog);
            });
    },
    load: function() {
        var self = this;
        if (this.isLoaded()) {
            return $.Deferred().resolve();
        }
        return $.get('/api/readlogs/recent').then(function(resp) {
            var readlogs = _.map(_.reverse(resp.data),
                function(it) {
                    return {
                        nid: it.novel_id,
                        aid: it.article_id,
                        idx: it.article_idx,
                        title: it.title
                    };
                });
            self.merge(readlogs);
            self.setLoaded();
        });
    },
    isLoaded: function() {
        var key = getChannelScopeKey('readlog.loaded');
        var loaded = localStorage.getItem(key);
        return loaded === '1';
    },
    setLoaded: function() {
        var key = getChannelScopeKey('readlog.loaded');
        localStorage.setItem(key, 1);
    },
    _serialize: function(readlogs) {
        var key = getChannelScopeKey('readlog');
        localStorage.setItem(key, JSON.stringify(readlogs));
    },
    _deserialize: function() {
        var key = getChannelScopeKey('readlog');
        var json = localStorage.getItem(key);
        return json ? JSON.parse(json) : [];
    }
}; (function() {
    var inovel = window.inovel = window.inovel || {};
    inovel.miniapp = {
        getBase64WxCode: function() {
            var defer = $.Deferred();
            var cacheKey = 'miniapp.wxcode';
            var code = LocalCache.get(cacheKey);
            if (code) {
                defer.resolve(code);
            } else {
                $.get('/v1/miniprogram/qrcode').then(function(resp) {
                    LocalCache.set(cacheKey, resp.data, {
                        ttl: 30 * 24 * 60 * 60
                    });
                    defer.resolve(resp.data);
                }).fail(function(xhr) {
                    defer.reject(JSON.parse(xhr.responseText));
                });
            }
            return defer.promise();
        }
    };
})();
var HandlebarTemplates = {
    _cache: {},
    get: function(name) {
        if (!this._cache[name]) {
            this._cache[name] = Handlebars.compile($('#' + name).html());
        }
        return this._cache[name];
    }
};
$.ajaxSetup({
    beforeSend: function(xhr) {
        var token = getMemberToken();
        if (token) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }
        if (window.__channelId) {
            xhr.setRequestHeader('X-Channel-ID', window.__channelId);
        }
    }
});