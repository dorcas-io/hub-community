var app = {
    currentUser: {},
    currentCompany: {},
    currentRole: 'member',
    isSearch: false,
    urlPrefix: {
        access_grants: '/access-grants'
    },
    utilities: {
        getElementAttributes: function (target) {
            if (!target.hasAttributes()) {
                return [];
            }
            var attributes = {};
            var attrs = target.attributes;
            for (var i = 0; i < attrs.length; i++) {
                attributes[attrs[i].name] = attrs[i].value;
            }
            return attributes;
        },
        randomId: function (length) {
            length = typeof length === 'undefined' || isNaN(length) ? 7 : parseInt(length, 10);
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for (var i = 0; i < length; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        },
        roleNamesFromArray: function (user) {
            if (typeof user.roles === 'undefined' || user.roles === null) {
                return [];
            }
            return user.roles.map(function (e) {return v.titleCase(e); });
        },
    },
    formatters: {
        access_grants: function (row, index) {
            let user = typeof row.user !== 'undefined' && row.user.data !== null ? row.user.data : null;
            if (user !== null) {
                row.avatar = '<div class="avatar d-block" style="background-image: url(' + row.photo + ')">';
                if (row.photo === null) {
                    row.avatar += row.firstname.substr(0, 1) + row.lastname.substr(0, 1);
                }
                row.avatar+= '</div>';
                row.name = user.firstname + ' ' + user.lastname;
                row.email = user.email;
            }
            row.modules_count = typeof row.extra_json.modules !== 'undefined' ? row.extra_json.modules.length : 0;
            row.pending_modules_count = typeof row.extra_json.pending_modules !== 'undefined' ? row.extra_json.pending_modules.length : 0;
            row.menu = '';
            if (app.currentUser.uuid !== row.id) {
                row.menu = '<div style="font-size: 1.2rem; margin-left: 10px;">';
                row.menu += '<a href="#" class="btn btn-icon"><i data-action="edit" data-index="' + index + '"  class="fe fe-eye"></i></a>';
                row.menu += '<a href="#" class="btn btn-icon"><i  data-action="delete" data-index="' + index + '" class="fe fe-trash-2"></i></a>';
                row.menu += '</div>';
            }
            return row;
        }
    }
};
