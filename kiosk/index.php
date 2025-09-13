<?php
require_once __DIR__ . '/config.php';

$isOpenAIConfigured = !empty(getenv('OPENAI_API_KEY'));
?>
<!doctype html>
<html lang="mr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>महाराष्ट्र योजना किओस्क</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/styles.css" rel="stylesheet">
</head>
<body data-openai-enabled="<?= $isOpenAIConfigured ? 'true' : 'false' ?>">
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<div class="container-fluid">
			<span class="navbar-brand">महाराष्ट्र योजना किओस्क</span>
		</div>
	</nav>

	<div class="container py-4">
		<div class="row mb-3">
			<div class="col">
				<h1 class="h4 mb-0">व्हिडिओ निवडा</h1>
				<p class="text-muted mb-0">बटणावर क्लिक केल्यावर संबंधित व्हिडिओ सुरू होईल. व्हिडिओ पूर्ण झाल्यावर खाली संबंधित प्रश्नोत्तरे दिसतील.</p>
			</div>
		</div>

		<div class="row g-3 kiosk-btns mb-4">
			<div class="col-12 col-md-4">
				<button class="btn btn-outline-primary w-100 video-btn" data-video-id="arogya">आरोग्य योजना</button>
			</div>
			<div class="col-12 col-md-4">
				<button class="btn btn-outline-primary w-100 video-btn" data-video-id="shikshan">शिक्षण योजना</button>
			</div>
			<div class="col-12 col-md-4">
				<button class="btn btn-outline-primary w-100 video-btn" data-video-id="krushi">कृषी योजना</button>
			</div>
		</div>

		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<video id="videoPlayer" class="w-100" controls preload="metadata" playsinline>
					<!-- src is set by JS after clicking a button -->
					आपला ब्राउझर व्हिडिओ प्लेयरला समर्थन देत नाही.
				</video>
			</div>
		</div>

		<div id="faqSection" class="d-none">
			<h2 class="h5">संबंधित प्रश्नोत्तरे</h2>
			<div id="faqContainer" class="accordion"></div>
		</div>
	</div>

	<!-- Chatbot launcher (floating) -->
	<button id="openChatbotBtn" class="btn btn-primary chatbot-fab" data-bs-toggle="modal" data-bs-target="#chatbotModal">
		योजना Chatbot
	</button>

	<!-- Chatbot Modal -->
	<div class="modal fade" id="chatbotModal" tabindex="-1" aria-labelledby="chatbotModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="chatbotModalLabel">योजना Chatbot (मराठी)</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row g-3">
						<div class="col-12 col-lg-5">
							<p class="fw-semibold mb-2">पूर्वनिश्चित प्रश्न निवडा:</p>
							<div id="predefinedQuestions" class="list-group small">
								<button type="button" class="list-group-item list-group-item-action" data-question="महात्मा फुले जनआरोग्य योजना (MPJAY) अंतर्गत कोण पात्र आहे?">महात्मा फुले जनआरोग्य योजना (MPJAY) अंतर्गत कोण पात्र आहे?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="MAHADBT शिष्यवृत्ती अर्ज करण्याची प्रक्रिया काय आहे?">MAHADBT शिष्यवृत्ती अर्ज करण्याची प्रक्रिया काय आहे?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="शेतकरी पिकविमा योजनेत नोंदणी व दावे कसे करायचे?">शेतकरी पिकविमा योजनेत नोंदणी व दावे कसे करायचे?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="अपंग व्यक्तींसाठी शासनाच्या लाभ योजना कोणत्या आहेत?">अपंग व्यक्तींसाठी शासनाच्या लाभ योजना कोणत्या आहेत?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="MHADA गृहयोजना अर्ज पात्रता व कागदपत्रे कोणती?">MHADA गृहयोजना अर्ज पात्रता व कागदपत्रे कोणती?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="विद्यार्थ्यांसाठी शिक्षण शुल्क सवलत/अनुदान कुठे मिळते?">विद्यार्थ्यांसाठी शिक्षण शुल्क सवलत/अनुदान कुठे मिळते?</button>
								<button type="button" class="list-group-item list-group-item-action" data-question="महिला व बालकल्याण विभागाच्या प्रमुख योजना कोणत्या?">महिला व बालकल्याण विभागाच्या प्रमुख योजना कोणत्या?</button>
							</div>
						</div>
						<div class="col-12 col-lg-7">
							<p class="fw-semibold mb-2">उत्तर:</p>
							<div class="border rounded p-3 bg-light" style="min-height: 160px;">
								<div id="answerLoading" class="d-none">
									<div class="d-flex align-items-center gap-2">
										<div class="spinner-border spinner-border-sm text-primary" role="status"></div>
										<span>उत्तर तयार होत आहे...</span>
									</div>
								</div>
								<div id="answerText" class="small text-body"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		window.OPENAI_ENABLED = <?= $isOpenAIConfigured ? 'true' : 'false' ?>;
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="assets/app.js"></script>
</body>
</html>

