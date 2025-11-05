;(function(root, Msg) {
    // 模块化导出
    if (typeof exports === 'object' && typeof module !== 'undefined') {
        module.exports = Msg;
    } else if (typeof define === 'function' && define.amd) {
        define([], () => Msg(root));
    } else {
        root.Qmsg = Msg(root);
    }
})(this, function(global) {
    'use strict';

    // ===== 工具函数与兼容性处理 =====
    // Object.assign 兼容
    if (typeof Object.assign !== 'function') {
        Object.assign = function(target) {
            if (target == null) {
                throw new TypeError('Cannot convert undefined or null to object');
            }
            target = Object(target);
            for (let i = 1; i < arguments.length; i++) {
                const source = arguments[i];
                if (source != null) {
                    for (const key in source) {
                        if (Object.prototype.hasOwnProperty.call(source, key)) {
                            target[key] = source[key];
                        }
                    }
                }
            }
            return target;
        };
    }

    // classList 兼容处理（优化正则匹配）
    if (!('classList' in HTMLElement.prototype)) {
        Object.defineProperty(HTMLElement.prototype, 'classList', {
            get: function() {
                const self = this;
                const trim = (str) => str.replace(/^\s+|\s+$/g, '');
                return {
                    add(cls) {
                        if (!this.contains(cls)) {
                            self.className = trim(self.className + ' ' + cls);
                        }
                    },
                    remove(cls) {
                        if (this.contains(cls)) {
                            self.className = trim(
                                self.className.replace(new RegExp(`(^|\\s+)${cls}(\\s+|$)`, 'g'), ' ')
                            );
                        }
                    },
                    contains(cls) {
                        return new RegExp(`(^|\\s+)${cls}(\\s+|$)`).test(self.className);
                    },
                    toggle(cls) {
                        this.contains(cls) ? this.remove(cls) : this.add(cls);
                    }
                };
            }
        });
    }

    // ===== 常量定义 =====
    const PLUGIN_NAME = 'qmsg';
    const NAMESPACE = (global && global.QMSG_GLOBALS && global.QMSG_GLOBALS.NAMESPACE) || PLUGIN_NAME;
    const STATES = {
        opening: 'MessageMoveIn',
        done: '',
        closing: 'MessageMoveOut'
    };

    // 默认配置（增强类型校验）
    const DEFAULTS = Object.assign({
        position: 'center', // center/right/left
        type: 'info', // info/warning/success/error/loading
        showClose: false,
        timeout: 2500,
        animation: true,
        autoClose: true,
        content: '',
        onClose: null,
        maxNums: 5,
        html: false,
        className: '', // 新增：自定义类名
        zIndex: 9999 // 新增：层级控制
    }, global && global.QMSG_GLOBALS && global.QMSG_GLOBALS.DEFAULTS);

    // 图标定义（使用函数返回，避免重复创建DOM）
    const getIcon = (type) => {
        const icons = {
            info: '<svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M24 44C29.5228 44 34.5228 41.7614 38.1421 38.1421C41.7614 34.5228 44 29.5228 44 24C44 18.4772 41.7614 13.4772 38.1421 9.85786C34.5228 6.23858 29.5228 4 24 4C18.4772 4 13.4772 6.23858 9.85786 9.85786C6.23858 13.4772 4 18.4772 4 24C4 29.5228 6.23858 34.5228 9.85786 38.1421C13.4772 41.7614 18.4772 44 24 44Z" fill="#1890ff" stroke="#1890ff" stroke-width="4" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24 11C25.3807 11 26.5 12.1193 26.5 13.5C26.5 14.8807 25.3807 16 24 16C22.6193 16 21.5 14.8807 21.5 13.5C21.5 12.1193 22.6193 11 24 11Z" fill="#FFF"/><path d="M24.5 34V20H23.5H22.5" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 34H28" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            warning: '<svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M24 44C29.5228 44 34.5228 41.7614 38.1421 38.1421C41.7614 34.5228 44 29.5228 44 24C44 18.4772 41.7614 13.4772 38.1421 9.85786C34.5228 6.23858 29.5228 4 24 4C18.4772 4 13.4772 6.23858 9.85786 9.85786C6.23858 13.4772 4 18.4772 4 24C4 29.5228 6.23858 34.5228 9.85786 38.1421C13.4772 41.7614 18.4772 44 24 44Z" fill="#faad14" stroke="#faad14" stroke-width="4" stroke-linejoin="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24 37C25.3807 37 26.5 35.8807 26.5 34.5C26.5 33.1193 25.3807 32 24 32C22.6193 32 21.5 33.1193 21.5 34.5C21.5 35.8807 22.6193 37 24 37Z" fill="#FFF"/><path d="M24 12V28" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            error: '<svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M24 44C35.0457 44 44 35.0457 44 24C44 12.9543 35.0457 4 24 4C12.9543 4 4 12.9543 4 24C4 35.0457 12.9543 44 24 44Z" fill="#f5222d" stroke="#f5222d" stroke-width="4" stroke-linejoin="round"/><path d="M29.6569 18.3431L18.3432 29.6568" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.3432 18.3431L29.6569 29.6568" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            success: '<svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M24 4L29.2533 7.83204L35.7557 7.81966L37.7533 14.0077L43.0211 17.8197L41 24L43.0211 30.1803L37.7533 33.9923L35.7557 40.1803L29.2533 40.168L24 44L18.7467 40.168L12.2443 40.1803L10.2467 33.9923L4.97887 30.1803L7 24L4.97887 17.8197L10.2467 14.0077L12.2443 7.81966L18.7467 7.83204L24 4Z" fill="#52c41a" stroke="#52c41a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 24L22 29L32 19" stroke="#FFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            loading: '<svg class="animate-turn" width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M4 24C4 35.0457 12.9543 44 24 44V44C35.0457 44 44 35.0457 44 24C44 12.9543 35.0457 4 24 4" stroke="#1890ff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M36 24C36 17.3726 30.6274 12 24 12C17.3726 12 12 17.3726 12 24C12 30.6274 17.3726 36 24 36V36" stroke="#1890ff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            close: '<svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="48" fill="white" fill-opacity="0.01"/><path d="M14 14L34 34" stroke="#909399" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 34L34 14" stroke="#909399" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        };
        return icons[type] || icons.info;
    };

    // 检测动画支持（优化判断逻辑）
    const CAN_ANIMATION = (() => {
        const style = document.createElement('div').style;
        const prefixes = ['animationName', 'WebkitAnimationName', 'MozAnimationName', 'msAnimationName', 'OAnimationName'];
        return prefixes.some(prefix => style[prefix] !== undefined);
    })();

    // 命名空间工具函数
    const namespacify = (...args) => [NAMESPACE, ...args].join('-');

    // ===== 消息实例类 =====
    class Msg {
        constructor(opts) {
            this.settings = Object.assign({}, DEFAULTS, opts || {});
            this.id = Qmsg.instanceCount;
            this.timer = null;
            this.state = ''; // 当前状态

            // 处理超时时间
            this.timeout = this._parseTimeout(this.settings.timeout);
            
            // 创建DOM元素
            this.$elem = this._createElement();
            this.$wrapper = this._getWrapper();
            this.$wrapper.appendChild(this.$elem);

            // 设置层级
            this.$elem.style.zIndex = this.settings.zIndex;

            // 初始化状态
            this._setState('opening');

            // 绑定关闭按钮事件
            if (this.settings.showClose) {
                this.$elem.querySelector(`.${namespacify('icon-close')}`)?.addEventListener('click', () => this.close());
            }

            // 绑定动画结束事件
            this.$elem.addEventListener('animationend', (e) => this._handleAnimationEnd(e));

            // 自动关闭逻辑
            if (this.settings.autoClose) {
                this._startAutoCloseTimer();
            }
        }

        // 解析超时时间（增强容错）
        _parseTimeout(timeout) {
            const num = parseInt(timeout, 10);
            return isNaN(num) || num < 0 ? DEFAULTS.timeout : num;
        }

        // 创建消息元素
        _createElement() {
            const $elem = document.createElement('div');
            const icon = getIcon(this.settings.type);
            const contentCls = [
                namespacify(`content-${this.settings.type}`),
                this.settings.showClose ? namespacify('content-with-close') : '',
                this.settings.className // 追加自定义类名
            ].filter(Boolean).join(' ');

            // 处理内容（XSS防护增强）
            const content = this.settings.content || '';
            const $contentSpan = document.createElement('span');
            if (this.settings.html) {
                $contentSpan.innerHTML = content;
            } else {
                $contentSpan.textContent = content;
            }

            // 关闭按钮
            const closeIcon = this.settings.showClose ? 
                `<i class="${namespacify('icon')} ${namespacify('icon-close')}">${getIcon('close')}</i>` : '';

            $elem.innerHTML = `
                <div class="${namespacify('content')}">
                    <div class="${contentCls}">
                        <i class="${namespacify('icon')}">${icon}</i>
                        ${$contentSpan.outerHTML}
                        ${closeIcon}
                    </div>
                </div>
            `;

            $elem.classList.add(namespacify('item'));
            $elem.style.textAlign = this.settings.position;
            
            return $elem;
        }

        // 获取或创建容器
        _getWrapper() {
            let $wrapper = document.querySelector(`.${NAMESPACE}`);
            if (!$wrapper) {
                $wrapper = document.createElement('div');
                $wrapper.classList.add(NAMESPACE, namespacify('wrapper'), namespacify('is-initialized'));
                document.body.appendChild($wrapper);
            }
            return $wrapper;
        }

        // 设置状态
        _setState(state) {
            if (!STATES[state]) return;
            this.state = state;
            const animation = this.settings.animation ? STATES[state] : '';
            this.$elem.style.animationName = animation;
            this.$elem.style.webkitAnimationName = animation;
        }

        // 处理动画结束
        _handleAnimationEnd(e) {
            if (e.animationName === STATES.closing) {
                this.destroy();
            }
            this.$elem.style.animationName = '';
            this.$elem.style.webkitAnimationName = '';
        }

        // 启动自动关闭定时器
        _startAutoCloseTimer() {
            const interval = 10;
            this.timer = setInterval(() => {
                this.timeout -= interval;
                if (this.timeout <= 0) {
                    this.close();
                }
            }, interval);

            // 鼠标悬停暂停
            this.$elem.addEventListener('mouseenter', () => {
                clearInterval(this.timer);
            });

            // 鼠标离开重启
            this.$elem.addEventListener('mouseleave', () => {
                if (this.state !== 'closing') {
                    this._startAutoCloseTimer();
                }
            });
        }

        // 销毁元素
        destroy() {
            clearInterval(this.timer);
            this.$elem?.parentNode?.removeChild(this.$elem);
            Qmsg.remove(this.id);
        }

        // 关闭消息
        close() {
            if (this.state === 'closing') return; // 防止重复关闭
            this._setState('closing');
            
            // 不支持动画时直接销毁
            if (!CAN_ANIMATION) {
                this.destroy();
            } else {
                Qmsg.remove(this.id);
            }

            // 执行关闭回调
            if (typeof this.settings.onClose === 'function') {
                this.settings.onClose.call(this);
            }
        }
    }

    // ===== 主插件对象 =====
    const Qmsg = {
        version: '1.0.0', // 升级版本号
        instanceCount: 0,
        oMsgs: [],
        maxNums: DEFAULTS.maxNums,

        // 配置全局参数
        config(cfg) {
            if (cfg && typeof cfg === 'object') {
                Object.assign(DEFAULTS, cfg);
                this.maxNums = DEFAULTS.maxNums > 0 ? parseInt(DEFAULTS.maxNums, 10) : 5;
            }
        },

        // 合并参数
        _mergeArgs(txt, config) {
            const opts = Object.assign({}, DEFAULTS);
            if (arguments.length === 0) return opts;
            
            if (typeof txt === 'object') {
                return Object.assign(opts, txt);
            } else {
                opts.content = String(txt);
            }
            
            if (config && typeof config === 'object') {
                return Object.assign(opts, config);
            }
            
            return opts;
        },

        // 判断重复消息
        _judgeReMsg(params) {
            const optStr = JSON.stringify(params);
            let existingMsg = null;

            // 查找相同配置的消息
            for (const key in this.oMsgs) {
                if (this.oMsgs[key].config === optStr) {
                    existingMsg = this.oMsgs[key].inst;
                    break;
                }
            }

            if (existingMsg) {
                // 更新计数
                existingMsg.count = existingMsg.count ? 
                    (existingMsg.count >= 99 ? 99 : existingMsg.count + 1) : 2;
                this._setMsgCount(existingMsg);
                return existingMsg;
            }

            // 创建新消息
            this.instanceCount++;
            const msg = new Msg(params);
            msg.id = this.instanceCount;
            msg.count = '';
            this.oMsgs[this.instanceCount] = {
                id: this.instanceCount,
                config: optStr,
                inst: msg
            };

            // 超出最大数量时关闭最早的消息
            this._limitMessageCount();

            return msg;
        },

        // 限制消息数量
        _limitMessageCount() {
            const msgList = Object.values(this.oMsgs);
            if (msgList.length > this.maxNums) {
                const excess = msgList.length - this.maxNums;
                // 按创建时间排序，关闭最早的
                msgList.sort((a, b) => a.id - b.id)
                    .slice(0, excess)
                    .forEach(item => item.inst.settings.autoClose && item.inst.close());
            }
        },

        // 设置消息计数
        _setMsgCount(msg) {
            let $count = msg.$elem.querySelector(`.${namespacify('count')}`);
            if (!$count) {
                $count = document.createElement('span');
                $count.classList.add(namespacify('count'));
                msg.$elem.querySelector(`.${namespacify('content')}`)?.appendChild($count);
            }
            $count.textContent = msg.count;
            // 重置动画
            $count.style.animation = 'none';
            $count.offsetHeight; // 触发重绘
            $count.style.animation = 'MessageShake 0.5s';
            // 重置超时时间
            msg.timeout = msg.settings.timeout;
        },

        // 移除消息引用
        remove(id) {
            if (this.oMsgs[id]) {
                delete this.oMsgs[id];
            }
        },

        // 关闭所有消息
        closeAll() {
            Object.values(this.oMsgs).forEach(item => item.inst.close());
        },

        // 消息类型方法
        info(txt, config) {
            const params = this._mergeArgs(txt, config);
            params.type = 'info';
            return this._judgeReMsg(params);
        },

        warning(txt, config) {
            const params = this._mergeArgs(txt, config);
            params.type = 'warning';
            return this._judgeReMsg(params);
        },

        success(txt, config) {
            const params = this._mergeArgs(txt, config);
            params.type = 'success';
            return this._judgeReMsg(params);
        },

        error(txt, config) {
            const params = this._mergeArgs(txt, config);
            params.type = 'error';
            return this._judgeReMsg(params);
        },

        loading(txt, config) {
            const params = this._mergeArgs(txt, config);
            params.type = 'loading';
            params.autoClose = false; // 加载中默认不自动关闭
            return this._judgeReMsg(params);
        }
    };

    return Qmsg;
});