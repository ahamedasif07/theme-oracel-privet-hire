<?php
/*
Template Name: Contact
*/
global $pageTitle, $pageDescription, $canonical;
$pageTitle = "Contact — Oracle Private Hire";
$pageDescription = "Get in touch with Oracle Private Hire. 24/7 phone, email, WhatsApp and live chat. Nationwide UK chauffeur service.";
$canonical = "/contact.php";
get_header();

$inputCls = "w-full rounded-xl border border-white/10 bg-ink/60 px-4 py-3.5 text-sm text-foreground placeholder:text-muted-foreground focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold transition-colors";

$contactSent = false;
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['contact_submit'] ) && isset( $_POST['oracle_contact_nonce'] ) && wp_verify_nonce( $_POST['oracle_contact_nonce'], 'oracle_contact_form' ) ) {
    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $subject = sanitize_text_field( $_POST['subject'] ?? 'New contact form message' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );

    $to      = get_option( 'admin_email' );
    $body    = "New message from the website contact form:\n\n"
             . "Name: {$name}\n"
             . "Phone: {$phone}\n"
             . "Email: {$email}\n\n"
             . "Message:\n{$message}\n";
    $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
    if ( $email ) {
        $headers[] = 'Reply-To: ' . $email;
    }

    $contactSent = wp_mail( $to, '[Contact Form] ' . $subject, $body, $headers );
}
?>

<section class="relative flex min-h-[62vh] items-end overflow-hidden pt-32 pb-16">
  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about.jpg" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40">
  <div class="absolute inset-0 bg-gradient-to-b from-ink/70 via-ink/60 to-ink"></div>
  <div class="relative mx-auto w-full max-w-7xl px-6 lg:px-10">
    <div class="flex items-center gap-3">
      <span class="h-px w-8 bg-gold"></span>
      <span class="text-xs uppercase tracking-[0.32em] text-gold">Contact</span>
    </div>
    <h1 class="mt-4 max-w-3xl font-display text-4xl leading-[1.05] text-foreground md:text-6xl">We're here, 24 hours a day.</h1>
    <p class="mt-5 max-w-2xl text-base text-muted-foreground md:text-lg">
      Speak with our reservations team by phone, email or WhatsApp — or send us a message and we'll respond within minutes.
    </p>
  </div>
</section>

<section class="mx-auto max-w-7xl px-6 py-20 lg:px-10 lg:py-28">
  <div class="grid gap-10 lg:grid-cols-[1fr_1.2fr]">
    <div class="space-y-4">
      <?php
      $contacts = [
        ['icon' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>', 'label' => 'Reservations', 'value' => '+44 1234 567 890', 'href' => 'tel:+441234567890'],
        ['icon' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>', 'label' => 'Email', 'value' => 'bookings@oracleprivatehire.co.uk', 'href' => 'mailto:bookings@oracleprivatehire.co.uk'],
        ['icon' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>', 'label' => 'WhatsApp', 'value' => 'Message us instantly', 'href' => 'https://wa.me/441234567890'],
        ['icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>', 'label' => 'Emergency Line', 'value' => '+44 1234 999 000', 'href' => 'tel:+441234999000'],
        ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>', 'label' => 'Business Hours', 'value' => '24 / 7 · 365 days a year', 'href' => null],
        ['icon' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>', 'label' => 'Service Area', 'value' => 'United Kingdom — nationwide', 'href' => null],
      ];
      foreach ($contacts as $c):
        $inner = '
          <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-gold/30 bg-gold/5 text-gold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $c['icon'] . '</svg>
          </div>
          <div class="min-w-0">
            <p class="text-xs uppercase tracking-[0.24em] text-muted-foreground">' . $c['label'] . '</p>
            <p class="mt-1 truncate text-sm text-foreground/90">' . $c['value'] . '</p>
          </div>';
        if ($c['href']): ?>
          <a href="<?php echo $c['href']; ?>" class="glass-card flex items-center gap-4 rounded-2xl p-5"><?php echo $inner; ?></a>
        <?php else: ?>
          <div class="glass-card flex items-center gap-4 rounded-2xl p-5"><?php echo $inner; ?></div>
        <?php endif;
      endforeach; ?>
    </div>

    <?php if ($contactSent): ?>
      <div class="glass-card rounded-3xl p-10 text-center">
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-gold text-ink">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <h2 class="mt-6 font-display text-2xl">Message sent.</h2>
        <p class="mt-3 text-muted-foreground">Thanks for reaching out — our team will reply shortly.</p>
      </div>
    <?php else: ?>
      <form class="glass-card space-y-5 rounded-3xl p-8 md:p-10" method="post" action="">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-xs uppercase tracking-widest text-muted-foreground">Name</label>
            <input name="name" class="<?php echo $inputCls; ?>" required>
          </div>
          <div>
            <label class="mb-2 block text-xs uppercase tracking-widest text-muted-foreground">Phone</label>
            <input type="tel" name="phone" class="<?php echo $inputCls; ?>" required>
          </div>
        </div>
        <div>
          <label class="mb-2 block text-xs uppercase tracking-widest text-muted-foreground">Email</label>
          <input type="email" name="email" class="<?php echo $inputCls; ?>" required>
        </div>
        <div>
          <label class="mb-2 block text-xs uppercase tracking-widest text-muted-foreground">Subject</label>
          <input name="subject" class="<?php echo $inputCls; ?>">
        </div>
        <div>
          <label class="mb-2 block text-xs uppercase tracking-widest text-muted-foreground">Message</label>
          <textarea name="message" rows="6" class="<?php echo $inputCls; ?>" required></textarea>
        </div>
        <?php wp_nonce_field( 'oracle_contact_form', 'oracle_contact_nonce' ); ?>
        <input type="hidden" name="contact_submit" value="1">
        <button type="submit" class="w-full rounded-full btn-gold px-8 py-4 text-sm">Send Message</button>
      </form>
    <?php endif; ?>
  </div>

  <div class="mt-16 overflow-hidden rounded-3xl border border-white/5">
    <iframe title="Service area map"
            src="https://www.openstreetmap.org/export/embed.html?bbox=-8.65%2C49.9%2C1.76%2C58.7&amp;layer=mapnik"
            class="grayscale-map h-[420px] w-full" loading="lazy"></iframe>
  </div>
</section>

<?php get_footer(); ?>
