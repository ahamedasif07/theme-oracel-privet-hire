<?php
/*
Template Name: Booking
*/
global $pageTitle, $pageDescription, $canonical;
$pageTitle = "Book Your Chauffeur — Oracle Private Hire";
$pageDescription = "Book your executive private hire or airport transfer. Fixed prices, instant confirmation, nationwide UK service.";
$canonical = "/booking.php";
get_header();

$inputCls = "w-full rounded-xl border border-white/10 bg-ink/60 px-4 py-3.5 text-sm text-black placeholder:text-muted-foreground focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold transition-colors";

// Server-side handling: process the booking on final submit and email the team.
$bookingSubmitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_submit']) && isset($_POST['oracle_booking_nonce']) && wp_verify_nonce($_POST['oracle_booking_nonce'], 'oracle_booking_form')) {
  $fields = [
    'Journey type'    => sanitize_text_field($_POST['journey_type'] ?? ''),
    'Passengers'      => sanitize_text_field($_POST['passengers'] ?? ''),
    'Pickup address'  => sanitize_text_field($_POST['pickup_address'] ?? ''),
    'Destination'     => sanitize_text_field($_POST['destination'] ?? ''),
    'Pickup date'     => sanitize_text_field($_POST['pickup_date'] ?? ''),
    'Pickup time'     => sanitize_text_field($_POST['pickup_time'] ?? ''),
    'Suitcases'       => sanitize_text_field($_POST['suitcases'] ?? ''),
    'Flight number'   => sanitize_text_field($_POST['flight_number'] ?? ''),
    'Vehicle'         => sanitize_text_field($_POST['vehicle'] ?? ''),
    'Special requests' => sanitize_textarea_field($_POST['special_requests'] ?? ''),
    'Full name'       => sanitize_text_field($_POST['full_name'] ?? ''),
    'Phone'           => sanitize_text_field($_POST['phone'] ?? ''),
    'Email'           => sanitize_email($_POST['email'] ?? ''),
  ];

  $to   = get_option('admin_email');
  $body = "New booking request from the website:\n\n";
  foreach ($fields as $label => $value) {
    $body .= "{$label}: {$value}\n";
  }
  $headers = ['Content-Type: text/plain; charset=UTF-8'];
  if (! empty($fields['Email'])) {
    $headers[] = 'Reply-To: ' . $fields['Email'];
  }

  $bookingSubmitted = wp_mail($to, 'New Booking Request — Oracle Private Hire', $body, $headers);
}
?>

<section class="relative flex min-h-[62vh] items-end overflow-hidden pt-32 pb-16">
  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero.jpg" alt=""
    class="absolute inset-0 h-full w-full object-cover opacity-40">
  <div class="absolute inset-0 bg-gradient-to-b from-ink/70 via-ink/60 to-ink"></div>
  <div class="relative mx-auto w-full max-w-7xl px-6 lg:px-10">
    <div class="flex items-center gap-3">
      <span class="h-px w-8 bg-gold"></span>
      <span class="text-xs uppercase tracking-[0.32em] text-gold">Reserve</span>
    </div>
    <h1 class="mt-4 max-w-3xl font-display text-4xl leading-[1.05] text-foreground md:text-6xl">Book your journey.
    </h1>
    <p class="mt-5 max-w-2xl text-base text-muted-foreground md:text-lg">
      Complete the form below for an instant fixed-price quote. A confirmation will be sent to your inbox within
      minutes.
    </p>
  </div>
</section>

<section class="mx-auto max-w-7xl px-6 py-20 lg:px-10 lg:py-28">
  <div class="mx-auto max-w-4xl">

    <!-- Progress -->
    <div id="booking-progress" class="mb-10 flex items-center justify-center gap-2 sm:gap-4">
      <?php $stepNames = ["Journey", "Vehicle", "Details"]; ?>
      <?php foreach ($stepNames as $i => $s): ?>
        <div class="flex items-center gap-2 sm:gap-4">
          <div class="flex items-center gap-3">
            <span
              class="step-indicator grid h-9 w-9 place-items-center rounded-full border text-sm font-medium transition-all <?php echo $i === 0 ? 'border-gold bg-gold text-ink' : 'border-white/20 text-muted-foreground'; ?>">
              <?php echo $i + 1; ?>
            </span>
            <span
              class="step-label hidden text-xs uppercase tracking-widest sm:inline <?php echo $i === 0 ? 'text-gold' : 'text-muted-foreground'; ?>"><?php echo $s; ?></span>
          </div>
          <?php if ($i < count($stepNames) - 1): ?>
            <span class="step-connector h-px w-8 sm:w-16 bg-white/10"></span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Success panel (shown after client-side submit) -->
    <div id="booking-success" class="glass-card hidden rounded-3xl p-12 text-center">
      <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gold text-ink">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 6 9 17l-5-5" />
        </svg>
      </div>
      <h2 class="mt-6 font-display text-3xl">Booking received.</h2>
      <p class="mt-3 text-muted-foreground">
        Thank you. Our team will confirm your journey and driver
        assignment shortly by email and SMS.
      </p>
    </div>

    <?php if ($bookingSubmitted): ?>
      <div class="glass-card rounded-3xl p-12 text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gold text-ink">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6 9 17l-5-5" />
          </svg>
        </div>
        <h2 class="mt-6 font-display text-3xl">Booking received.</h2>
        <p class="mt-3 text-muted-foreground">
          Thank you. Our team will confirm your journey and driver
          assignment shortly by email and SMS.
        </p>
      </div>
    <?php else: ?>
      <form id="booking-form" class="glass-card space-y-6 rounded-3xl p-8 md:p-12" method="post" action="">

        <!-- Step 1: Journey -->
        <div class="booking-step active grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Journey
              Type</label>
            <select name="journey_type" class="<?php echo $inputCls; ?>" required>
              <option value="oneway">One-way</option>
              <option value="return">Return</option>
              <option value="hourly">Hourly hire</option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Number of
              Passengers</label>
            <input type="number" name="passengers" min="1" max="8" value="1" class="<?php echo $inputCls; ?>"
              required>
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Pickup
              Address</label>
            <input type="text" name="pickup_address" placeholder="e.g. 10 Downing St, London"
              class="<?php echo $inputCls; ?>" required>
          </div>
          <div class="md:col-span-2">
            <label
              class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Destination</label>
            <input type="text" name="destination" placeholder="e.g. Heathrow Terminal 5"
              class="<?php echo $inputCls; ?>" required>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Pickup
              Date</label>
            <input type="date" name="pickup_date" class="<?php echo $inputCls; ?>" required>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Pickup
              Time</label>
            <input type="time" name="pickup_time" class="<?php echo $inputCls; ?>" required>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Suitcases</label>
            <input type="number" name="suitcases" min="0" max="10" value="1" class="<?php echo $inputCls; ?>">
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Flight Number
              (optional)</label>
            <input type="text" name="flight_number" placeholder="e.g. BA286" class="<?php echo $inputCls; ?>">
          </div>
        </div>

        <!-- Step 2: Vehicle -->
        <div class="booking-step grid gap-4 sm:grid-cols-3">
          <?php
          $vehicleOptions = [
            ['name' => 'Executive Saloon', 'desc' => 'Up to 3 pax · 2 bags', 'price' => 'from £55'],
            ['name' => 'Luxury MPV', 'desc' => 'Up to 7 pax · 7 bags', 'price' => 'from £85'],
            ['name' => 'Prestige SUV', 'desc' => 'Up to 4 pax · 4 bags', 'price' => 'from £95'],
          ];
          foreach ($vehicleOptions as $i => $v): ?>
            <label
              class="cursor-pointer rounded-2xl border border-white/10 bg-ink/60 p-6 transition-all hover:border-gold has-[:checked]:border-gold has-[:checked]:bg-gold/5">
              <input type="radio" name="vehicle" value="<?php echo $v['name']; ?>"
                <?php echo $i === 0 ? 'checked' : ''; ?> class="sr-only">
              <h3 class="font-display text-xl"><?php echo $v['name']; ?></h3>
              <p class="mt-1 text-xs text-muted-foreground"><?php echo $v['desc']; ?></p>
              <p class="mt-4 font-display text-2xl text-gradient-gold"><?php echo $v['price']; ?></p>
            </label>
          <?php endforeach; ?>
          <div class="sm:col-span-3">
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Special
              Requests</label>
            <textarea name="special_requests" rows="3" placeholder="Child seat, additional stop, etc."
              class="<?php echo $inputCls; ?>"></textarea>
          </div>
        </div>

        <!-- Step 3: Details -->
        <div class="booking-step grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your name"
              class="<?php echo $inputCls; ?> !text-black !bg-white placeholder:!text-muted-foreground"
              required>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Phone</label>
            <input type="tel" name="phone" placeholder="Enter your phone"
              class="<?php echo $inputCls; ?> !text-black !bg-white placeholder:!text-muted-foreground"
              required>
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-xs uppercase tracking-[0.2em] text-muted-foreground">Email</label>
            <input type="email" name="email" placeholder="Enter your email"
              class="<?php echo $inputCls; ?> !text-black !bg-white placeholder:!text-muted-foreground"
              required>
          </div>

        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 pt-4">
          <button type="button" id="booking-back" disabled
            class="inline-flex items-center gap-2 rounded-full btn-ghost-gold px-6 py-3 text-sm disabled:opacity-40 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m12 19-7-7 7-7" />
              <path d="M19 12H5" />
            </svg>
            Back
          </button>
          <button type="submit" id="booking-next"
            class="inline-flex items-center gap-2 rounded-full btn-gold px-8 py-3.5 text-sm">
            Continue
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg>
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>