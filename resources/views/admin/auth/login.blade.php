@extends('admin.layouts.plain')

@section('content')
<style>
	html, body {
		margin: 0;
		padding: 0;
		height: 100%;
		overflow: hidden;
		font-family: 'Nunito', sans-serif;
	}

	#particles-js {
		position: fixed;
		width: 100%;
		height: 100%;
		background: linear-gradient(135deg, #0066cc, #33ccff);
		z-index: 0;
	}

	.login-container {
		position: relative;
		z-index: 2;
		background: rgba(255, 255, 255, 0.1);
		backdrop-filter: blur(15px);
		border: 1px solid rgba(255, 255, 255, 0.3);
		border-radius: 20px;
		padding: 40px 30px;
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
		text-align: center;
		width: 100%;
		max-width: 400px;
		margin: auto;
		top: 50%;
		transform: translateY(-50%);
		color: #fff;
		animation: slideFadeIn 1s ease-out;
	}

	h1 {
		font-weight: 700;
		color: #ffffff;
		margin-bottom: 10px;
	}

	.account-subtitle {
		color: #e0e0e0;
		margin-bottom: 20px;
	}

	.form-control {
		border-radius: 30px;
		padding: 12px 20px;
		font-size: 1rem;
		border: none;
		margin-bottom: 15px;
		transition: box-shadow 0.3s ease;
	}

	.form-control:focus {
		outline: none;
		box-shadow: 0 0 8px rgba(51, 204, 255, 0.7);
	}

	.btn-block {
		border-radius: 30px;
		font-weight: bold;
		font-size: 1.1rem;
		transition: background-color 0.3s ease;
	}

	.btn-block:hover {
		background-color: #005bb5;
	}

	.forgotpass, .dont-have {
		margin-top: 15px;
		font-size: 0.9rem;
	}

	.forgotpass a, .dont-have a {
		color: #ffffff;
		font-weight: bold;
		text-decoration: underline;
		transition: color 0.3s ease;
	}

	.forgotpass a:hover, .dont-have a:hover {
		color: #99ddff;
	}

	.animated-icon {
		width: 60px;
		margin-bottom: 15px;
		animation: pulse 3s ease-in-out infinite;
		transform-origin: center center;
	}

	@keyframes pulse {
		0%, 100% {
			transform: scale(1);
		}
		50% {
			transform: scale(1.08);
		}
	}

	@keyframes slideFadeIn {
		from {
			transform: translateY(30px);
			opacity: 0;
		}
		to {
			transform: translateY(0);
			opacity: 1;
		}
	}
</style>

<!-- Background Animation -->
<div id="particles-js"></div>

<!-- Login Form -->
<div class="login-container">
	<img src="https://cdn-icons-png.flaticon.com/512/3771/3771392.png" alt="Doctor Icon" class="animated-icon">
	<h1>Welcome Back!</h1>
	<p class="account-subtitle">Login Panel</p>

	@if (session('login_error'))
		<x-alerts.danger :error="session('login_error')" />
	@endif

	<form action="{{ route('login') }}" method="POST">
		@csrf
		<input class="form-control" name="email" type="email" placeholder="Email" required>
		<input class="form-control" name="password" type="password" placeholder="Password" required>
		<button class="btn btn-success btn-block w-100" type="submit">Login</button>
	</form>

	<div class="forgotpass"><a href="{{ route('password.request') }}">Forgot Password?</a></div>
	<div class="dont-have">Don’t have an account? <a href="{{ route('register') }}">Register</a></div>
</div>

<!-- Particle JS Script -->
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
particlesJS("particles-js", {
	"particles": {
		"number": {
			"value": 60,  // Slightly fewer particles for less visual noise
			"density": {
				"enable": true,
				"value_area": 900
			}
		},
		"color": {
			"value": "#ffffff"
		},
		"shape": {
			"type": "circle",
			"stroke": {
				"width": 0,
				"color": "#000000"
			}
		},
		"opacity": {
			"value": 0.4,  // softer opacity
			"random": true,
			"anim": {
				"enable": true,
				"speed": 0.5,
				"opacity_min": 0.2,
				"sync": false
			}
		},
		"size": {
			"value": 3,
			"random": true,
			"anim": {
				"enable": true,
				"speed": 2,
				"size_min": 1,
				"sync": false
			}
		},
		"line_linked": {
			"enable": true,
			"distance": 160,
			"color": "#ffffff",
			"opacity": 0.3,
			"width": 1
		},
		"move": {
			"enable": true,
			"speed": 1.5,  // slower movement for smoothness
			"direction": "none",
			"random": true,
			"straight": false,
			"out_mode": "out",
			"bounce": false,
			"attract": {
				"enable": false,
				"rotateX": 600,
				"rotateY": 1200
			}
		}
	},
	"interactivity": {
		"detect_on": "canvas",
		"events": {
			"onhover": {
				"enable": true,
				"mode": "grab"
			},
			"onclick": {
				"enable": false
			},
			"resize": true
		},
		"modes": {
			"grab": {
				"distance": 140,
				"line_linked": {
					"opacity": 0.8
				}
			}
		}
	},
	"retina_detect": true
});
</script>
@endsection
