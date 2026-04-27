(async function () {
	const scriptTag = document.currentScript;
	const url = new URL(scriptTag.getAttribute('src'));
	const iframeWidth = scriptTag.getAttribute('data-iframe-width');
	const iframeHeight = scriptTag.getAttribute('data-iframe-height');
	let language = scriptTag.getAttribute('data-language') ?? 'en';
	const chatbotHostOrigin = `${url.origin}`;
	const chatBotUuid = scriptTag.getAttribute('data-chatbot-uuid');
	const iFrameUrl = `${chatbotHostOrigin}/chatbot/${chatBotUuid}/frame`;
	const jsonUrl = `${chatbotHostOrigin}/api/v2/chatbot/${chatBotUuid}`;
	let sessionId = null; // Will be set by iframe
	let isTrustedDomain = true;
	const currentDomain = window.location.hostname;

	if (document.querySelector('html')?.getAttribute('lang')) {

		const htmlLang = document.querySelector('html').getAttribute('lang');

		if (htmlLang) {
			language = htmlLang;
		}
	}

	const defaultConfig = {
		active: false,
		color: '#763ed1',
		trigger_avatar_size: '60px',
		avatar: null,
		trigger_background: ''
	};

	async function getChatbotDetails() {
		if (chatBotUuid) {
			try {
				const response = await fetch(`${jsonUrl}?language=${language}`, {
					headers: { 'Accept': 'application/json' }
				});

				if (!response.ok) {
					console.error('Chatbot API returned status:', response.status);
					return null;
				}

				const data = await response.json();
				const chatbot = data?.data || {};
				const domains = chatbot?.trusted_domains;

				if (domains?.length && !domains.includes(currentDomain)) {
					isTrustedDomain = false;
				}

				return {
					...defaultConfig,
					...chatbot
				};
			} catch (error) {
				console.error('Failed to fetch chatbot details:', error);
				return null;
			}
		}
		return null;
	}

	const config = await getChatbotDetails();

	if (!config) {
		console.error('Failed to load chatbot configuration');
		return;
	}

	if (!iFrameUrl) {
		console.error('Iframe source is not set');
		return;
	}

	if (!isTrustedDomain){
		console.log(`🚫 Chatbot blocked: "${currentDomain}" is not in the list of trusted domains.`);
		return;
	}

	function getBubbleCloseButton() {

		return `<button id="lqd-ext-chatbot-bubble-close" type="button" aria-label="Close">
			<svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path d="M0.8 8L0 7.2L3.2 4L0 0.8L0.8 0L4 3.2L7.2 0L8 0.8L4.8 4L8 7.2L7.2 8L4 4.8L0.8 8Z" />
			</svg>
		</button>`;
	}

	function getTriggerButton() {
		return `<button class="lqd-ext-chatbot-trigger" type="button">
			<img
				id="lqd-ext-chatbot-trigger-img"
				src="${config.avatar.startsWith('http') ? config.avatar : `${chatbotHostOrigin}${config.avatar.startsWith('/') ? config.avatar : `/${config.avatar}`}`}"
				alt="${config.title}"
			/>
			<span id="lqd-ext-chatbot-trigger-icon">
				<svg width="16" height="10" viewBox="0 0 16 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M8 9.07814L0.75 1.82814L2.44167 0.136475L8 5.69481L13.5583 0.136475L15.25 1.82814L8 9.07814Z"/>
				</svg>
			</span>
		</button>`;
	}

	function getBadgeHtml() {
		const badgeHtml = `<span class="lqd-ext-chatbot-badge">${config.unread_count}</span>`;

		return badgeHtml;
	}

	function getBubbleContent() {
		const bubbleDesign = config.bubble_design || 'plain';
		const bubbleMessage = config.bubble_message;
		let content = '';

		if ( bubbleDesign === 'suggestions' ) {
			const prompts = config.suggested_prompts?.slice(0, 3) || [];
			const promptButtons = prompts.map((prompt, i) => `
				<button class="lqd-ext-chatbot-suggestion-btn" type="button" data-prompt="${prompt.prompt || ''}" data-index="${i}">
					${prompt.name || prompt.prompt || 'Prompt'}
				</button>`).join('');

			content = `
				${bubbleMessage ? `<div class="lqd-ext-chatbot-trigger-bubble-content-fill"><p class="lqd-ext-chatbot-trigger-bubble-message" data-has-fill="true">${bubbleMessage}</p></div>` : ''}
				${promptButtons ? `
					<div class="lqd-ext-chatbot-suggestions-wrapper">
						${promptButtons}
					</div>
				` : ''}`;
		} else if ( bubbleDesign === 'links' ) {
			const links = config.links || {};
			const linkButtons = [];

			if (links.whatsapp) {
				linkButtons.push(`
					<a class="lqd-ext-chatbot-link-btn" href="${links.whatsapp}" target="_blank" rel="noopener noreferrer">
						WhatsApp
						<svg width="17" height="17" viewBox="0 0 17 17" fill="currentColor" fill-opacity="0.75" xmlns="http://www.w3.org/2000/svg"><path d="M8.4375 0C3.78506 0 0 3.78479 0 8.4375C0 10.0745 0.469391 11.6526 1.36038 13.0232L0.0455933 16.0911C-0.0450439 16.3021 0.00219727 16.5476 0.164795 16.7102C0.272461 16.8179 0.416382 16.875 0.5625 16.875C0.637207 16.875 0.712738 16.8602 0.784149 16.8294L3.85208 15.5143C5.22235 16.4059 6.80054 16.875 8.4375 16.875C13.0902 16.875 16.875 13.0902 16.875 8.4375C16.875 3.78479 13.0902 0 8.4375 0ZM12.7683 11.4576C12.7683 11.4576 12.0668 12.3574 11.5598 12.5678C10.2711 13.1012 8.45178 12.5678 6.37921 10.4958C4.30719 8.42322 3.77353 6.60388 4.30719 5.31519C4.51758 4.80762 5.41736 4.10669 5.41736 4.10669C5.66125 3.91663 6.04028 3.94025 6.25891 4.15887L7.27679 5.17676C7.49542 5.39539 7.49542 5.75354 7.27679 5.97217L6.63794 6.61047C6.63794 6.61047 6.37921 7.38721 7.93323 8.94177C9.48724 10.4958 10.2645 10.2371 10.2645 10.2371L10.9028 9.59821C11.1215 9.37958 11.4796 9.37958 11.6982 9.59821L12.7161 10.6161C12.9348 10.8347 12.9584 11.2132 12.7683 11.4576Z"/></svg>
					</a>
				`);
			}

			if (links.telegram) {
				linkButtons.push(`
					<a class="lqd-ext-chatbot-link-btn" href="${links.telegram}" target="_blank" rel="noopener noreferrer">
						Telegram
						<svg width="18" height="15" viewBox="0 0 18 15" fill="currentColor" fill-opacity="0.75 xmlns="http://www.w3.org/2000/svg"><path d="M15.6496 0.124822C13.3937 1.05902 3.72105 5.06564 1.04841 6.1577C-0.744034 6.85718 0.305186 7.51292 0.305186 7.51292C0.305186 7.51292 1.83519 8.03744 3.14667 8.43092C4.45815 8.8244 5.15763 8.38718 5.15763 8.38718L11.3215 4.23404C13.5073 2.74778 12.9828 3.97178 12.4582 4.4963C11.3215 5.633 9.44181 7.42526 7.86807 8.86796C7.16859 9.47996 7.51833 10.0047 7.82433 10.2669C8.96103 11.2287 12.0648 13.1959 12.2397 13.3271C13.1633 13.9809 14.9799 14.9221 15.2562 12.9336L16.3491 6.07022C16.6989 3.75326 17.0486 1.61108 17.0923 0.999082C17.2234 -0.487178 15.6496 0.124822 15.6496 0.124822Z"/></svg>
					</a>
				`);
			}

			if ( linkButtons.length ) {
				content = `<div class="lqd-ext-chatbot-trigger-bubble-content-fill">${linkButtons.join('')}</div>`;
			}
		} else if ( bubbleDesign === 'modern' ) {
			content = `<div class="lqd-ext-chatbot-trigger-bubble-content-fill"><p class="lqd-ext-chatbot-trigger-bubble-message" data-has-fill="true">${bubbleMessage}</p></div>`;
		} else if ( bubbleDesign === 'promo_banner' ) {
			const promo = config.promo_banner || {};
			const promoImage = promo.image
				? `<div class="lqd-ext-chatbot-promo-image">
					<img src="${promo.image}" alt="" />
				</div>`
				: '';
			const promoTitle = promo.title
				? `<h4 class="lqd-ext-chatbot-promo-title">${promo.title}</h4>`
				: '';
			const promoDesc = promo.description
				? `<p class="lqd-ext-chatbot-promo-desc">${promo.description}</p>`
				: '';
			const promoBtn = promo.btn_label
				? `<div class="lqd-ext-chatbot-promo-btn-wrap"><a class="lqd-ext-chatbot-promo-btn" href="${promo.btn_link || '#'}" target="_blank" rel="noopener noreferrer">${promo.btn_label}</a></div>`
				: '';

			content = `<div class="lqd-ext-chatbot-promo">
				${promoImage}
				<div class="lqd-ext-chatbot-promo-content">
					${promoTitle}
					${promoDesc}
				</div>
				${promoBtn}
			</div>`;
		} else if ( bubbleDesign !== 'blank' && bubbleMessage && bubbleMessage.trim() ) {
			content = `<p class="lqd-ext-chatbot-trigger-bubble-message" data-has-fill="${[ 'plain' ].includes(bubbleDesign) ? 'true' : 'false'}">${bubbleMessage}</p>`;
		}

		return content;
	}

	// Helper function to generate bubble/trigger markup based on design
	function getBubbleMarkup() {
		const bubbleDesign = config.bubble_design || 'plain';
		const triggerButton = getTriggerButton();
		const badgeHtml = getBadgeHtml();
		const closeButton = getBubbleCloseButton();
		const bubbleContent = getBubbleContent();
		let content = '';

		content = bubbleContent;

		if ([ 'modern', 'suggestions', 'links', 'promo_banner' ].includes(bubbleDesign)) {
			content = `${closeButton} ${content}`;
		}

		return `${content ? `<div class="lqd-ext-chatbot-trigger-bubble" data-hide="false" data-style="${bubbleDesign}" data-interactive="${[ 'suggestions', 'links', 'promo_banner' ].includes(bubbleDesign) ? 'true' : 'false'}">${content}</div>` : ''}
		${triggerButton} ${badgeHtml}`;
	}

	const widgetMarkup = `
<div id="lqd-ext-chatbot-wrap" data-ready="false" data-window-open="false" data-has-unread="${config.unread_count > 0 ? 'true' : 'false'}">
    <style>
        #lqd-ext-chatbot-wrap {
            --lqd-ext-chat-trigger-background: ${config.trigger_background && config.trigger_background !== '' ? config.trigger_background : 'var(--lqd-ext-chat-primary)'};
            --lqd-ext-chat-trigger-foreground: ${config.trigger_foreground && config.trigger_foreground !== '' ? config.trigger_foreground : 'var(--lqd-ext-chat-primary-foreground)'};
            --lqd-ext-chat-trigger-size: ${config.trigger_avatar_size || '60px'};
            display: flex;
            flex-direction: column;
            gap: var(--lqd-ext-chat-window-y-offset, 20px);
            position: fixed;
            bottom: var(--lqd-ext-chat-offset-y, 30px);
            left: var(--lqd-ext-chat-offset-y, 30px);
            z-index: 9999;
            transition: transform 0.3s, opacity 0.3s, visibility 0.3s;
            font-family: var(--lqd-ext-chat-font-family, 'inherit');
            pointer-events: none;
        }

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger {
            display: inline-grid;
            place-items: center;
            place-content: center;
            width: var(--lqd-ext-chat-trigger-size);
            height: var(--lqd-ext-chat-trigger-size);
            padding: 0;
            position: relative;
            background-color: var(--lqd-ext-chat-trigger-background);
            color: var(--lqd-ext-chat-trigger-foreground);
            border-radius: var(--lqd-ext-chat-trigger-size);
            border: none;
            overflow: hidden;
            transition: all 0.15s;
            cursor: pointer;
            backdrop-filter: blur(12px) saturate(120%);
            pointer-events: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
        }
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger:before {
            content: '';
            display: inline-block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: var(--lqd-ext-chat-primary);
            opacity: 0;
            transform: translateY(3px);
            transition: all 0.15s;
        }
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-mobile {
            display: inline-grid;
			place-items: center;
			width: 24px;
			height: 24px;
			position: fixed;
			top: 17px;
			inset-inline-end: 16px;
			z-index: 999991;
			visibility: hidden;
			opacity: 0;
			border-radius: 20px;
			background-color: hsl(0 0% 0% / 35%);
			color: #fff;
			backdrop-filter: blur(6px);
			transition: all 0.3s;
        }
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-mobile:before {
			content: none;
		}
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-img,
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-icon {
            grid-row: 1 / 1;
            grid-column: 1 / 1;
            transition: all 0.15s;
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-img {
            width: var(--lqd-ext-chat-trigger-size);
            height: var(--lqd-ext-chat-trigger-size);
            max-width: none;
            position: relative;
            z-index: 1;
            object-fit: cover;
            border-radius: 50%;
        }
        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-trigger-icon {
            opacity: 0;
            transform: translateY(3px);
        }
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger:active {
            transform: scale(0.9);
        }

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-iframe-wrap {
            width: min(var(--lqd-ext-chat-window-w), calc(100vw - (var(--lqd-ext-chat-offset-x) * 2)));
            height: min(var(--lqd-ext-chat-window-h), calc(100vh - (var(--lqd-ext-chat-offset-y) * 2) - var(--lqd-ext-chat-trigger-h) - var(--lqd-ext-chat-window-y-offset)));
            box-shadow: 0 5px 40px hsl(0 0% 0% / 16%);
            border-radius: 12px;
            pointer-events: none;
            transform-origin: bottom left;
            transform: scale(0.975) translateY(6px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.1s;
        }

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-iframe {
            width: 100%;
            height: 100%;
        }

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble {
            position: absolute;
            bottom: calc(var(--lqd-ext-chat-trigger-size) + var(--lqd-ext-chat-window-y-offset));
            left: 0;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2em;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: all 0.15s;
        }

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble,
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble:before,
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble:after,
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble *,
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble *:before,
        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble *:after {
            box-sizing: border-box;
        }

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble-message {
			position: relative;
			z-index: 1;
			margin: 0;
			padding: 15px 20px;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble-content-fill {
			padding: 8px;
            border-radius: 8px;
			background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
		}

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-wrap[data-window-state=close] .lqd-ext-chatbot-trigger-bubble {
            bottom: calc(var(--lqd-ext-chat-trigger-size) + 12px);
        }

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble[data-interactive=true] {
			pointer-events: auto;
		}

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-bubble-close {
            position: absolute;
            bottom: 100%;
            right: 0;
            width: 34px;
            height: 34px;
            padding: 0;
			margin-bottom: 10px;
            display: inline-grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 34px;
            cursor: pointer;
            transition: all 0.15s;
            z-index: 2;
            pointer-events: auto;
			color: #000;
        }

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-bubble-close:hover {
			background-color: #000;
			color: #fff;
            transform: scale(1.1);
        }

        #lqd-ext-chatbot-wrap #lqd-ext-chatbot-bubble-close svg {
            width: 10px;
            height: 10px;
        }

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-link-btn {
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 270px;
			height: 52px;
			border-radius: 12px;
			padding: 16px 24px;
			background-color: #F4F5F5;
			color: #000;
			font-size: 12px;
			font-weight: 500;
			text-decoration: none;
			transition: all 0.3s;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-link-btn:not(:last-child) {
			margin-bottom: 8px;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-link-btn:hover {
			background-color: #000;
			color: #fff;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble-message[data-has-fill=true] {
			background-color: rgb(0 0 0 / 4%);
			color: #000;
			font-size: 14px;
			font-weight: 500;
			border-radius: 8px;
			padding: 14px 20px;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble[data-style=plain] {
			border-radius: 8px;
			backdrop-filter: blur(12px);
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-suggestions-wrapper {
			min-width: min(285px, calc(100vw - (var(--lqd-ext-chat-offset-x) * 2)));
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			margin-top: 10px;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-suggestion-btn {
			display: flex;
			padding: 12px 20px;
			border-radius: 5em;
			font-size: 14px;
			font-weight: 500;
			position: relative;
			z-index: 1;
			background: none;
			border: none;
			box-shadow: none;
			outline: none;
			color: var(--lqd-ext-chat-primary);
			backdrop-filter: blur(12px);
			overflow: hidden;
			cursor: pointer;
			transition: all 0.3s;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-suggestion-btn:before {
			content: '';
			display: inline-block;
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			background-color: var(--lqd-ext-chat-primary);
			opacity: 0.1;
			z-index: -1;
			transition: all 0.3s;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-suggestion-btn:hover {
			color: var(--lqd-ext-chat-primary-foreground);
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-suggestion-btn:hover:before {
			opacity: 1;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger-bubble[data-style=promo_banner] {
			padding: 0;
			backdrop-filter: none;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo {
			width: min(280px, calc(100vw - (var(--lqd-ext-chat-offset-x) * 2)));
			overflow: hidden;
			border-radius: 16px;
			background: #fff;
			box-shadow: 0 22px 44px rgba(0, 0, 0, 0.05);
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-image {
			width: 100%;
			height: 155px;
			overflow: hidden;
			border-radius: 12px 12px 0 0;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-image img {
			max-width: 100%;
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-content {
			padding: 12px 24px;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-title {
			margin: 0 0 10px;
			font-size: 15px;
			line-height: 1.2em;
			font-weight: 600;
			color: #000;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-desc {
			margin: 0;
			font-size: 14px;
			line-height: 1.42;
			color: rgba(0, 0, 0, 0.5);
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-btn-wrap {
			border-top: 1px solid rgb(0 0 0 / 5%);
			text-align: center;
		}

		#lqd-ext-chatbot-wrap .lqd-ext-chatbot-promo-btn {
			display: block;
			padding: 18px 14px;
			color: var(--lqd-ext-chat-primary);
			font-size: 14px;
			font-weight: 500;
			text-decoration: underline;
			text-underline-offset: 4px;
			text-decoration-thickness: 0.65px;
		}

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-badge {
            display: none;
            place-items: center;
            min-width: 20px;
            height: 20px;
            padding: 3px;
            border-radius: 20px;
            position: absolute;
            bottom: calc(var(--lqd-ext-chat-trigger-size) - 19px);
            left: calc(var(--lqd-ext-chat-trigger-size) - 20px);
            z-index: 10;
            background-color: #ef4444;
            color: white;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
			box-sizing: border-box;
        }

        #lqd-ext-chatbot-wrap .lqd-ext-chatbot-not-loaded {
            margin: 0;
            padding: 1rem;
        }

        #lqd-ext-chatbot-wrap[data-ready=true] .lqd-ext-chatbot-trigger:not(.lqd-ext-chatbot-trigger-mobile),
        #lqd-ext-chatbot-wrap[data-ready=true] .lqd-ext-chatbot-trigger-bubble {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        #lqd-ext-chatbot-wrap[data-ready=true] .lqd-ext-chatbot-trigger-bubble[data-hide=true] {
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
        }

        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-iframe-wrap {
            transform: translateY(0) scale(1);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #lqd-ext-chatbot-wrap[data-window-state=open] .lqd-ext-chatbot-trigger:before {
            transform: translateY(0);
            opacity: 1;
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-trigger-icon {
            opacity: 1;
            transform: translateY(0);
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] #lqd-ext-chatbot-trigger-img {
            opacity: 0;
            transform: translateY(-3px);
        }
        #lqd-ext-chatbot-wrap[data-window-state=open] .lqd-ext-chatbot-trigger-bubble {
            transform: scale(0.95);
            opacity: 0;
            visibility: hidden;
        }

        html[dir=rtl] #lqd-ext-chatbot-wrap[data-pos-x=left] {
            align-items: end;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] {
            left: auto;
            right: var(--lqd-ext-chat-offset-x, 30px);
            align-items: end;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] #lqd-ext-chatbot-iframe-wrap {
            transform-origin: bottom right;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] .lqd-ext-chatbot-trigger-bubble {
            left: auto;
            right: 0;
        }

        #lqd-ext-chatbot-wrap[data-pos-x=right] .lqd-ext-chatbot-badge {
            right: 0;
            left: auto;
        }

		html[dir=rtl] #lqd-ext-chatbot-wrap[data-pos-x=right] {
            align-items: start;
        }

        #lqd-ext-chatbot-wrap[data-pos-y=top] {
            bottom: auto;
            top: var(--lqd-ext-chat-offset-y, 30px);
            flex-direction: column-reverse;
        }

        #lqd-ext-chatbot-wrap[data-pos-y=top] .lqd-ext-chatbot-trigger-bubble {
            bottom: auto;
            top: calc(var(--lqd-ext-chat-trigger-h) + var(--lqd-ext-chat-window-y-offset));
        }

		#lqd-ext-chatbot-wrap[data-has-unread=true] .lqd-ext-chatbot-trigger {
			-webkit-mask-image: radial-gradient(circle 20px at calc(100% - 10px) 9px, transparent 12px, black 12px);
            mask-image: radial-gradient(circle 20px at calc(100% - 10px) 9px, transparent 12px, black 12px);
		}

		#lqd-ext-chatbot-wrap[data-has-unread=true] .lqd-ext-chatbot-badge {
			display: inline-grid;
		}

		@media (max-width: 768px) {
			#lqd-ext-chatbot-wrap {
				width: calc(100vw - (var(--lqd-ext-chat-offset-x) * 2));
				pointer-events: none;
			}
			#lqd-ext-chatbot-wrap .lqd-ext-chatbot-iframe-wrap,
			#lqd-ext-chatbot-wrap .lqd-ext-chatbot-trigger {
				pointer-events: auto;
			}
			#lqd-ext-chatbot-wrap #lqd-ext-chatbot-iframe-wrap {
				position: fixed;
				z-index: 99999;
				width: 100vw !important;
				height: 100vh !important;
				left: 0 !important;
				right: 0 !important;
				bottom: 0 !important;
				top: 0 !important;
			}
			#lqd-ext-chatbot-wrap[data-window-state="open"] .lqd-ext-chatbot-trigger-mobile {
				opacity: 1;
				visibility: visible;
			}
		}
    </style>
    <div id="lqd-ext-chatbot-iframe-wrap">
        ${iFrameUrl ? `
            <iframe
                src="${iFrameUrl}"
                title="${config.title}"
                frameborder="0"
                allowfullscreen
                allowtransparency
                allow="microphone"
                id="lqd-ext-chatbot-iframe"
                name="lqd-ext-chatbot-iframe"
                crossOrigin="anonymous"
                onload="
                    const wrapper = document.querySelector('#lqd-ext-chatbot-wrap');
                    window.addEventListener('message', event => {
                        if ( event.origin !== '${chatbotHostOrigin}' || event.data.type !== 'lqd-ext-chatbot-response-styling' || !wrapper ) return;
                        const { styles, attrs } = event.data.data;
                        Object.entries(styles).forEach(([key, value]) => {
                            if ( key === '--lqd-ext-chat-window-w' && ${iframeWidth ? true : false} ) {
                                return wrapper.style.setProperty(key, '${parseInt(iframeWidth, 10)}px');
                            } else if ( key === '--lqd-ext-chat-window-h' && ${iframeHeight ? true : false} ) {
                                return wrapper.style.setProperty(key, '${parseInt(iframeHeight, 10)}px');
                            }
                            wrapper.style.setProperty(key, value);
                        });
                        Object.entries(attrs).forEach(([key, value]) => {
                            wrapper.setAttribute(key, value);
                        });
                        wrapper.setAttribute('data-ready', 'true');
                    });

                    this.contentWindow.postMessage({
                        type: 'lqd-ext-chatbot-request-styling',
                    }, '${chatbotHostOrigin}');
                "
            ></iframe>` : `
        <p class="lqd-ext-chatbot-not-loaded">Could not setup the chatbot</p>
        `}
    </div>
    ${getBubbleMarkup()}

    <button
        class="lqd-ext-chatbot-trigger lqd-ext-chatbot-trigger-mobile"
        type="button"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" > <path d="M18 6l-12 12" /> <path d="M6 6l12 12" /> </svg>
    </button>
</div>`;

	document.body.insertAdjacentHTML('beforeend', widgetMarkup);

	const chatbotWrap = document.querySelector('#lqd-ext-chatbot-wrap');
	const triggers = document.querySelectorAll('.lqd-ext-chatbot-trigger');
	const iframe = document.querySelector('#lqd-ext-chatbot-iframe');
	let open = false;

	// Trigger button click handler
	triggers.forEach(trigger => {
		trigger.addEventListener('click', ev => {
			ev.preventDefault();
			open = !open;
			chatbotWrap.setAttribute('data-window-state', open ? 'open' : 'close');

			if (iframe && iframe.contentWindow) {
				iframe.contentWindow.postMessage({
					type: 'lqd-ext-chatbot-window-state',
					state: open ? 'open' : 'close',
				}, chatbotHostOrigin);
			}
		});
	});

	document.querySelector('#lqd-ext-chatbot-bubble-close')?.addEventListener('click', ev => {
		ev.preventDefault();
		ev.stopPropagation();
		ev.stopImmediatePropagation();

		const bubble = document.querySelector('.lqd-ext-chatbot-trigger-bubble');
		bubble?.setAttribute('data-hide', 'true');
	});

	// Suggestion buttons handler
	const suggestionBtns = document.querySelectorAll('.lqd-ext-chatbot-suggestion-btn');
	suggestionBtns.forEach(btn => {
		btn.addEventListener('click', ev => {
			ev.preventDefault();
			const prompt = btn.getAttribute('data-prompt');

			// Open the chatbot
			if (!open) {
				open = true;
				chatbotWrap.setAttribute('data-window-state', 'open');
			}

			// Send prompt to iframe after it opens
			setTimeout(() => {
				if (iframe && iframe.contentWindow && prompt) {
					iframe.contentWindow.postMessage({
						type: 'lqd-ext-chatbot-send-prompt',
						prompt: prompt
					}, chatbotHostOrigin);
				}
			}, 300);
		});
	});

	// Poll for unread messages count every 5 seconds
	async function updateUnreadBadge() {
		const chatbotWrap = document.querySelector('#lqd-ext-chatbot-wrap');

		try {
			// Add session as query parameter if available
			const url = sessionId
				? `${jsonUrl}?language=${language}&session=${sessionId}`
				: `${jsonUrl}?language=${language}`;

			const response = await fetch(url);
			const data = await response.json();
			const unreadCount = data?.data?.unread_count || 0;

			// Update all badges
			const badges = document.querySelectorAll('.lqd-ext-chatbot-badge');
			badges.forEach(badge => {
				if (unreadCount > 0) {
					badge.textContent = unreadCount;
					chatbotWrap.setAttribute('data-has-unread', 'true');
				} else {
					chatbotWrap.setAttribute('data-has-unread', 'false');
				}
			});
		} catch (error) {
			console.error('Failed to update unread count:', error);
		}
	}

	// Page visit tracking
	function recordPageVisit() {
		if (!sessionId || !chatBotUuid) return;
		const pageVisitUrl = `${chatbotHostOrigin}/api/v2/chatbot/${chatBotUuid}/session/${sessionId}/page-visit`;
		fetch(pageVisitUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
			body: JSON.stringify({
				page_url: window.location.href,
				page_title: document.title || null,
			}),
		}).catch(err => console.error('Failed to record page visit:', err));
	}

	function leavePageVisit() {
		if (!sessionId || !chatBotUuid) return;
		const pageVisitUrl = `${chatbotHostOrigin}/api/v2/chatbot/${chatBotUuid}/session/${sessionId}/page-visit`;
		const payload = JSON.stringify({ _method: 'PUT' });
		if (navigator.sendBeacon) {
			const blob = new Blob([ payload ], { type: 'application/json' });
			navigator.sendBeacon(pageVisitUrl, blob);
		} else {
			fetch(pageVisitUrl, {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
				keepalive: true,
			}).catch(() => {});
		}
	}

	window.addEventListener('beforeunload', leavePageVisit);

	// Listen for session ID from iframe
	window.addEventListener('message', event => {
		if (event.origin !== chatbotHostOrigin) return;

		if (event.data.type === 'lqd-ext-chatbot-session-id') {
			sessionId = event.data.sessionId;
			// Update badge immediately after receiving session ID
			updateUnreadBadge();
			// Record initial page visit
			recordPageVisit();
		}

		if (event.data.type === 'lqd-ext-chatbot-unread-change') {
			updateUnreadBadge();
		}
	});

	// Start polling
	// setInterval(updateUnreadBadge, 15000);
})();
