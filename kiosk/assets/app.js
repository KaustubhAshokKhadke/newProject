/* global bootstrap */
(function(){
	const videoSources = {
		arogya: 'assets/videos/arogya.mp4',
		shikshan: 'assets/videos/shikshan.mp4',
		krushi: 'assets/videos/krushi.mp4'
	};

	const faqByVideo = {
		arogya: [
			{ q: 'महात्मा फुले जनआरोग्य योजना कोणासाठी आहे?', a: 'गरीब व गरजू नागरिकांना निःशुल्क उपचार उपलब्ध करण्यासाठी ही योजना आहे.' },
			{ q: 'कागदपत्रे कोणती लागतात?', a: 'रहिवासी पुरावा, ओळखपत्र, पात्रता प्रमाणपत्र इत्यादी.' }
		],
		shikshan: [
			{ q: 'शिष्यवृत्ती कशी मिळेल?', a: 'MAHADBT पोर्टलवर नोंदणी करून पात्रतेनुसार अर्ज करा.' },
			{ q: 'कागदपत्रे कोणती?', a: 'आधार, जात प्रमाणपत्र (लागू असल्यास), उत्पन्न प्रमाणपत्र, मार्कशीट इ.' }
		],
		krushi: [
			{ q: 'पिकविमा योजनेत नोंदणी कशी?', a: 'CSC/बँक अथवा ऑनलाईन पोर्टलद्वारे नोंदणी करा.' },
			{ q: 'दावा कसा करायचा?', a: 'नुकसानीच्या घटनेनंतर नियत वेळेत संबंधित अधिकाऱ्यांना कळवा व आवश्यक कागदपत्रे जमा करा.' }
		]
	};

	const player = document.getElementById('videoPlayer');
	const faqSection = document.getElementById('faqSection');
	const faqContainer = document.getElementById('faqContainer');

	function renderFaq(videoId){
		const faqs = faqByVideo[videoId] || [];
		faqContainer.innerHTML = '';
		faqs.forEach((item, index) => {
			const accordionItem = document.createElement('div');
			accordionItem.className = 'accordion-item';
			const headerId = `faq-h-${videoId}-${index}`;
			const collapseId = `faq-c-${videoId}-${index}`;
			accordionItem.innerHTML = `
				<h2 class="accordion-header" id="${headerId}">
					<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
						${item.q}
					</button>
				</h2>
				<div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}" data-bs-parent="#faqContainer">
					<div class="accordion-body">${item.a}</div>
				</div>
			`;
			faqContainer.appendChild(accordionItem);
		});
		faqSection.classList.toggle('d-none', faqs.length === 0);
	}

	// Attach button listeners
	document.querySelectorAll('.video-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			const id = btn.getAttribute('data-video-id');
			const src = videoSources[id];
			if(!src){ return; }
			player.src = src;
			player.currentTime = 0;
			player.play().catch(()=>{});
			faqSection.classList.add('d-none');
			player.onended = function(){
				renderFaq(id);
			};
		});
	});

	// Chatbot logic
	const predefined = document.getElementById('predefinedQuestions');
	const answerText = document.getElementById('answerText');
	const answerLoading = document.getElementById('answerLoading');

	function setLoading(isLoading){
		answerLoading.classList.toggle('d-none', !isLoading);
		if(isLoading){ answerText.textContent = ''; }
	}

	async function askChatbot(question){
		setLoading(true);
		try {
			const resp = await fetch('chatbot.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ question })
			});
			if(!resp.ok){ throw new Error('Network error'); }
			const data = await resp.json();
			answerText.textContent = data.answer || 'क्षमस्व, उत्तर उपलब्ध नाही.';
		} catch (e){
			answerText.textContent = 'स्थानिक उत्तर: कृपया इंटरनेट/एपीआय की तपासा. '+ e.message;
		} finally {
			setLoading(false);
		}
	}

	if(predefined){
		predefined.addEventListener('click', (ev) => {
			const btn = ev.target.closest('button[data-question]');
			if(!btn) return;
			const q = btn.getAttribute('data-question');
			askChatbot(q);
		});
	}
})();

