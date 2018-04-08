layui.define(['layer', 'form', 'element', 'code', 'util'], function (exports) {
    var layer = layui.layer,
        element = layui.element,
        form = layui.form;

    form.on('submit(layform)', function (data) {
        $.post(data.form.action, data.field, function (res) {
            layer.msg(res.msg, {
                time: 1000,
            }, function () {
                if (res.code == 1) {
                    if (res.url) {
                        window.location.href = res.url
                    }
                }
            });
        });
        return false;
    });

    layui.code({
        title: 'JavaScript',
        height: '100px',
        about: false
    });

    element.on('tab(*)', function () {
        $(window).resize();
    });

    $('.ajax-delete').click(function () {
        var self = $(this);
        var href = $(this).attr('href');
        var title = $(this).attr('title');
        layer.confirm('您确定要 <span style="color:#f56954">' + title + '</span> 吗？', function (index) {
            $.get(href, function (res) {
                if (res.code == 1) {
                    self.closest('.layui-card').slideUp(500, function () {
                        self.closest('.layui-card').remove();
                    });
                } else {
                    layer.msg(res.msg);
                }
            });
            layer.close(index);
        });
        return false;
    });


    //展示一个列表
    $.fn.getList = function (callback, resetPage) {
        var that = this;
        var url = that.data('url') || window.location.href;
        var param = that.find('form').serialize();
        var page = that.data('page') || 1;
        var temp = layui.data('leacmf');
        var limit = that.data('limit') || temp.limit || 20;
        if (resetPage) {
            page = 1;
        }
        param = 'limit=' + limit + '&page=' + page + '&' + param;
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'html',
                data: param,
            })
            .done(function (html) {
                that.find('.data').empty().html(html);
                form.render();
                that.data('page', page);
            })
            .fail(function (xhr) {
                console.log(xhr.responseText);
                that.find('.data').empty().html('<p><i class="fa fa-warning"></i> 服务器异常，请稍后再试~</p>');
            })
            .always(function () {
                if (typeof callback === 'function') {
                    callback();
                }
                $(window).resize();
            });
    };

    /**
     * 异步获取表单
     * 异步提交表单
     * 表单验证
     */
    $(document).on('click', '.ajax-edit-api', function (event) {
        event.preventDefault();
        var self = $(this);
        if (self.attr('disabled')) return false;
        var url = self.attr('href') || self.data('url');
        if (!url) return;

        $.get(url, '_pid=' + (_pId || ''), function (html) {
            if (typeof html === 'object') {
                layer.msg(html.msg);
                if (html.url == 'reload') {
                    window.location.reload();
                } else if (html.url) {
                    window.location.href = res.url;
                } else {
                    if (self.attr('update') == 'all') {
                        $('.data-list').each(function () {
                            $(this).getList()
                        });
                    } else {
                        self.closest('.data-list').getList();
                    }
                }
                return false;
            }
            layer.open({
                type: 1,
                title: self.attr('title'),
                content: html,
                scrollbar: false,
                maxWidth: '80%',
                btn: ['确定', '取消'],
                yes: function (index, layero) {
                    if ($(layero).find('.layui-layer-btn0').attr('disabled')) {
                        return false;
                    }
                    $(layero).find('.layui-layer-btn0').attr('disabled', 'disabled');
                    var _form = $(layero).find('form');
                    $.post(_form.attr('action'), '_pid=' + (_pId || '') + '&' + _form.serialize(), function (res) {
                        layer.msg(res.msg, {
                            time: 1500
                        }, function () {
                            if (res.code == 1) {
                                if (res.url == '/reload') {
                                    window.location.reload();
                                } else if (res.url) {
                                    window.location.href = res.url;
                                } else {
                                    if (self.attr('update') == 'all') {
                                        $('.data-list').each(function () {
                                            $(this).getList()
                                        });
                                    } else {
                                        self.closest('.data-list').getList();
                                    }
                                }
                                layer.close(index);
                            }
                        });
                        $(layero).find('.layui-layer-btn0').removeAttr('disabled')
                    }, 'json');
                },
                btn2: function (index) {
                    layer.close(index);
                },
                success: function () {
                    form.render();
                }
            }, 'html');
        });
        return false;
    });

    //删除操作
    $(document).on('click', '.ajax-set-api', function (event) {
        event.preventDefault();
        var self = $(this);
        var url = self.attr('href') || self.data('url');
        var param = [];
        if (self.attr('param')) {
            self.closest('.data-list').find('input:checkbox[name="_id"]:checked').each(function () {
                param.push($(this).val());
            });

            if (!param.length) {
                layer.msg('请选择要操作的数据');
                return false;
            }
            if (self.attr('single') && param.length != 1) {
                layer.msg('该操作只可选择一项进行操作');
                return false;
            }
        }
        param = param.join(',');
        layer.confirm('您确定要 <span style="color:red">' + self.attr('title') || self.text() + '</span> 吗？', function (index) {
            $.get(url, '_pid=' + (_pId || '') + '&_id=' + (param || ''), function (res) {
                if (res.code == 1) {
                    if (res.url == '/reload') {
                        window.location.reload();
                    } else if (res.url) {
                        window.location.href = res.url;
                    } else {
                        if (self.attr('update') == 'all') {
                            $('.data-list').each(function () {
                                $(this).getList()
                            });
                        } else {
                            self.closest('.data-list').getList();
                        }
                    }
                    layer.close(index);
                }
                layer.msg(res.msg, {
                    time: 1000
                });

            });
            layer.close(index);
        });
        return false;
    });


    element.on('tabDelete(response)', function (data) {
        var id = $(this).closest('li').attr('lay-id');
        $.get('/response/delete', 'id=' + id);
    });

    $('.response').each(function () {
        var val = $(this).find('textarea').val()
        if (isJSON(val)) {
            $(this).find('pre').jsonViewer(eval('(' + val + ')'));
        } else {
            $(this).find('pre').addClass('layui-code').html(val);
        }
    });

    function isJSON(str) {
        if (typeof str == 'string') {
            try {
                var obj = JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        }
    }　

    var clipboard = new Clipboard('.copy');
    clipboard.on('success', function (e) {
        layer.tips('复制成功', e.trigger, {
            time: 3000,
            tips: 3
        });
    });
    clipboard.on('error', function (e) {
        layer.tips('复制出错，请手动复制！', e.trigger, {
            time: 3000,
            tips: 3
        });
    });



    /**
     * edit 
     */
    var edit = {
        open: function (url, title, callback) {
            $.get(url, function (html) {
                if (typeof html === 'object') {
                    layer.msg(html.msg);
                    window.location.href = '/';
                }
                layer.open({
                    type: 1,
                    title: title,
                    content: html,
                    scrollbar: false,
                    maxWidth: '80%',
                    success: function () {
                        form.render();
                        if (typeof callback === 'object') {
                            callback();
                        }
                    }
                }, 'html');
            });
            return false;
        }
    };

    if (is_login) {
        layui.util.fixbar({
            bar1: '&#xe857;',
            bar2: '&#xe612;',
            bgcolor: '#009688',
            click: function (type) {
                if (type === 'bar1') {
                    var url = '/project';
                }
                if (type === 'bar2') {
                    var url = '/user';
                }
                edit.open(url, '');
            }
        });
    }


    $(document).on('click', '.ajax-open', function () {
        var self = $(this);
        edit.open(self.attr('href') || self.data('url') || '', self.attr('title') || '');
        return false;
    });


    //邀请用户
    $(document).on('click', '.ajax-ask-api', function () {
        var self = $(this);
        $.post(self.attr('href') || self.data('url'), self.closest('form').serialize(), function (res) {
            if (res.code == 1) {
                self.closest('.data-list').getList();
            }
            layer.msg(res.msg);
        });
        return false;
    });


    //分页
    $(document).on('click', '.layui-laypage-page>a', function () {
        var page = $(this).attr('lay-page');
        $(this).closest('.data-list').data('page', page).getList();
        return false;
    });

    //tab 双击刷新
    $(document).on('dblclick', '.layui-tab-title>li', function () {
        if ($(this).closest('.layui-tab').find('.layui-show').find('.data-list').length) {
            $(this).closest('.layui-tab').find('.layui-show').find('.data-list').getList();
        }

    });

    //自动已读
    $('.site-tree').on('click', 'a', function () {
        $.get('/api/read', {
            _id: $(this).data('id'),
            _pid: window._pId
        });
        $(this).find('.layui-badge-dot').addClass('none');
    })

    //消息点击
    $(document).on('click', '.mine-msg blockquote a', function () {
        $.get($(this).attr('href'), function (res) {
            layer.msg(res.msg);
            return false;
        });
        return false;
    });

    exports('edit', edit);
});