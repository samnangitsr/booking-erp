import { Head } from "@inertiajs/react";
import { startTransition, useEffect, useState } from "react";

const initialData = {
    hero: {
        headline:
            "Search smarter stays, rates, and experiences in one booking flow.",
        subheadline:
            "An Agoda-inspired storefront powered by your Laravel backend data, built for hotels, apartments, and curated city escapes.",
    },
    stats: {
        approvedProperties: 0,
        featuredStays: 0,
        activeDestinations: 0,
        approvedReviews: 0,
    },
    destinations: [],
    featuredProperties: [],
    propertyTypes: [],
    reviews: [],
};

const fallbackDestinations = [
    {
        id: "demo-phnom-penh",
        name: "Phnom Penh",
        city: "Phnom Penh",
        country: "Cambodia",
        propertyCount: 24,
        imageUrl: null,
    },
    {
        id: "demo-siem-reap",
        name: "Siem Reap",
        city: "Siem Reap",
        country: "Cambodia",
        propertyCount: 18,
        imageUrl: null,
    },
    {
        id: "demo-kampot",
        name: "Kampot",
        city: "Kampot",
        country: "Cambodia",
        propertyCount: 11,
        imageUrl: null,
    },
    {
        id: "demo-bangkok",
        name: "Bangkok",
        city: "Bangkok",
        country: "Thailand",
        propertyCount: 37,
        imageUrl: null,
    },
    {
        id: "demo-da-nang",
        name: "Da Nang",
        city: "Da Nang",
        country: "Vietnam",
        propertyCount: 15,
        imageUrl: null,
    },
    {
        id: "demo-singapore",
        name: "Singapore",
        city: "Singapore",
        country: "Singapore",
        propertyCount: 21,
        imageUrl: null,
    },
];

const fallbackProperties = [
    {
        id: "demo-riverside",
        name: "Riverside Atelier Hotel",
        propertyType: "Boutique Hotel",
        description:
            "A polished central-city stay concept with riverside views, flexible inventory rules, and merchandising-ready content blocks.",
        location: "Daun Penh, Phnom Penh",
        address: "Quayside district, Phnom Penh",
        starRating: 4.6,
        reviewScore: 8.9,
        reviewCount: 248,
        startingPrice: 132,
        maxOccupancy: 3,
        isFeatured: true,
        imageUrl: null,
    },
    {
        id: "demo-sunrise",
        name: "Sunrise Courtyard Suites",
        propertyType: "Apartment",
        description:
            "Long-stay friendly units with clear pricing, occupancy indicators, and destination-led positioning for urban explorers.",
        location: "Boeung Keng Kang, Phnom Penh",
        address: "BKK1, Phnom Penh",
        starRating: 4.2,
        reviewScore: 8.4,
        reviewCount: 184,
        startingPrice: 96,
        maxOccupancy: 4,
        isFeatured: true,
        imageUrl: null,
    },
    {
        id: "demo-angkor",
        name: "Angkor Horizon Retreat",
        propertyType: "Resort",
        description:
            "A leisure-focused showcase card with space for destination storytelling, family occupancy, and nightly rate highlights.",
        location: "Siem Reap, Cambodia",
        address: "Siem Reap central zone",
        starRating: 4.8,
        reviewScore: 9.1,
        reviewCount: 322,
        startingPrice: 178,
        maxOccupancy: 4,
        isFeatured: true,
        imageUrl: null,
    },
    {
        id: "demo-coast",
        name: "Coastline Social Stay",
        propertyType: "Hostel",
        description:
            "A lighter, social-led format for budget inventory that still keeps the OTA-style pricing and review hierarchy.",
        location: "Kampot, Cambodia",
        address: "Old market quarter, Kampot",
        starRating: 4.0,
        reviewScore: 8.0,
        reviewCount: 119,
        startingPrice: 42,
        maxOccupancy: 2,
        isFeatured: false,
        imageUrl: null,
    },
];

const fallbackReviews = [
    {
        id: "demo-review-1",
        rating: 9.0,
        title: "Fast to compare, easy to trust",
        comment:
            "The property cards make rate differences and neighborhood context obvious right away, which is exactly what guests need on first visit.",
        propertyName: "Riverside Atelier Hotel",
        city: "Phnom Penh",
        guestName: "Guest 01",
        date: "May 2026",
    },
    {
        id: "demo-review-2",
        rating: 8.7,
        title: "Better merchandising for apartments",
        comment:
            "Showing occupancy, stay type, and starting price together gives apartment inventory the same commercial clarity as hotel listings.",
        propertyName: "Sunrise Courtyard Suites",
        city: "Phnom Penh",
        guestName: "Guest 02",
        date: "May 2026",
    },
    {
        id: "demo-review-3",
        rating: 9.2,
        title: "Feels like a modern OTA landing page",
        comment:
            "Destination discovery, rate cues, and the trust signals all sit in the right order, so the storefront feels conversion-first.",
        propertyName: "Angkor Horizon Retreat",
        city: "Siem Reap",
        guestName: "Guest 03",
        date: "May 2026",
    },
];

const destinationGradients = [
    "from-[#0f5ea8] via-[#1677c9] to-[#66b5ff]",
    "from-[#ff7f50] via-[#ff9a45] to-[#ffd166]",
    "from-[#003049] via-[#1d4d7a] to-[#5ea3d8]",
    "from-[#4d148c] via-[#7b2cbf] to-[#c77dff]",
    "from-[#005f73] via-[#0a9396] to-[#94d2bd]",
    "from-[#7f5539] via-[#b56576] to-[#e56b6f]",
];

const propertyGradients = [
    "from-[#143d59] via-[#1f6e8c] to-[#84a7a1]",
    "from-[#582f0e] via-[#bc6c25] to-[#dda15e]",
    "from-[#3a0ca3] via-[#4361ee] to-[#4cc9f0]",
    "from-[#264653] via-[#2a9d8f] to-[#e9c46a]",
];

function formatPrice(amount) {
    if (amount === null || amount === undefined) {
        return "Contact for rate";
    }

    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        maximumFractionDigits: 0,
    }).format(amount);
}

function formatCompact(value) {
    return new Intl.NumberFormat("en", {
        notation: "compact",
        maximumFractionDigits: 1,
    }).format(value);
}

function ratingLabel(score) {
    if (score >= 9) return "Exceptional";
    if (score >= 8) return "Excellent";
    if (score >= 7) return "Very good";
    if (score > 0) return "Good";
    return "New";
}

function StarRow({ value }) {
    return (
        <div className="flex gap-1 text-[0.7rem] text-[#ef6b57]">
            {Array.from({ length: 5 }).map((_, index) => (
                <span
                    key={index}
                    className={
                        index < Math.round(value) ? "opacity-100" : "opacity-25"
                    }
                >
                    ★
                </span>
            ))}
        </div>
    );
}

function SurfaceImage({
    imageUrl,
    gradient,
    eyebrow,
    title,
    subtitle,
    compact = false,
}) {
    const style = imageUrl
        ? {
              backgroundImage: `linear-gradient(135deg, rgb(12 49 80 / 0.25), rgb(12 49 80 / 0.55)), url(${imageUrl})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
          }
        : undefined;

    return (
        <div
            className={`relative overflow-hidden rounded-[28px] ${imageUrl ? "bg-slate-800" : `bg-linear-to-br ${gradient}`}`}
            style={style}
        >
            <div
                className={`flex h-full flex-col justify-end p-5 text-white ${compact ? "min-h-45" : "min-h-70"}`}
            >
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.24),transparent_26%)]" />
                <div className="relative">
                    <p className="text-xs font-semibold uppercase tracking-[0.28em] text-white/75">
                        {eyebrow}
                    </p>
                    <h3 className="mt-2 text-xl font-semibold sm:text-2xl">
                        {title}
                    </h3>
                    <p className="mt-2 max-w-xs text-sm text-white/82">
                        {subtitle}
                    </p>
                </div>
            </div>
        </div>
    );
}

export default function Welcome({
    appName = "Booking ERP",
    locale = "en",
    apiUrl,
}) {
    const [homeData, setHomeData] = useState(initialData);
    const [status, setStatus] = useState("loading");
    const [selectedDestination, setSelectedDestination] = useState("");

    useEffect(() => {
        let cancelled = false;

        async function loadHomepage() {
            setStatus("loading");

            try {
                const response = await fetch(apiUrl, {
                    headers: { Accept: "application/json" },
                    cache: "no-store",
                });

                if (!response.ok) {
                    throw new Error("Failed to load homepage data");
                }

                const payload = await response.json();

                if (cancelled) {
                    return;
                }

                startTransition(() => {
                    setHomeData(payload);
                    setSelectedDestination(
                        payload.destinations?.[0]?.name ||
                            fallbackDestinations[0].name,
                    );
                });

                setStatus("ready");
            } catch (error) {
                if (!cancelled) {
                    setStatus("error");
                }
            }
        }

        loadHomepage();

        return () => {
            cancelled = true;
        };
    }, [apiUrl]);

    const copy =
        locale === "km"
            ? {
                  sectionLabel: "ផ្នែកខាងមុខថ្មី",
                  heroLead:
                      "ស្វែងរកកន្លែងស្នាក់នៅ អត្រាតម្លៃ និងបទពិសោធន៍ទេសចរណ៍ក្នុងច្រកការកក់តែមួយ។",
                  heroBody:
                      "ផ្ទាំងមុខរចនាបែប Agoda ជាមួយ Tailwind CSS 4 ដែលអានទិន្នន័យពី Laravel backend API របស់អ្នកដោយផ្ទាល់។",
                  destinationLabel: "គោលដៅកំពុងពេញនិយម",
                  featuredLabel: "កន្លែងស្នាក់នៅណែនាំ",
                  typesLabel: "ប្រភេទអចលនទ្រព្យ",
                  reviewsLabel: "សំឡេងពីភ្ញៀវ",
                  adminCta: "ចូលផ្ទាំងគ្រប់គ្រង",
                  searchButton: "ស្វែងរកអត្រាតម្លៃ",
              }
            : {
                  sectionLabel: "New storefront",
                  heroLead: homeData.hero.headline,
                  heroBody: homeData.hero.subheadline,
                  destinationLabel: "Trending destinations",
                  featuredLabel: "Featured stays",
                  typesLabel: "Stay categories",
                  reviewsLabel: "Guest pulse",
                  adminCta: "Open admin",
                  searchButton: "Explore rates",
              };

    const safeDestinations = homeData.destinations.length
        ? homeData.destinations
        : fallbackDestinations;
    const safeProperties = homeData.featuredProperties.length
        ? homeData.featuredProperties
        : fallbackProperties;
    const safeReviews = homeData.reviews.length
        ? homeData.reviews
        : fallbackReviews;

    const highlightProperty = safeProperties[0] || null;
    const highlightDestination =
        safeDestinations.find((item) => item.name === selectedDestination) ||
        safeDestinations[0] ||
        null;

    return (
        <>
            <Head title="Travel storefront" />

            <div className="relative overflow-hidden">
                <div className="pointer-events-none absolute inset-x-0 top-0 h-120 bg-[radial-gradient(circle_at_top,rgba(14,49,80,0.14),transparent_58%)]" />

                <div className="mx-auto max-w-330 px-4 pb-16 pt-5 sm:px-6 lg:px-8 lg:pb-24">
                    <header className="rounded-full border border-white/70 bg-white/75 px-4 py-3 shadow-[0_18px_50px_rgba(14,49,80,0.08)] backdrop-blur lg:px-6">
                        <div className="flex flex-wrap items-center justify-between gap-4">
                            <div className="flex items-center gap-3">
                                <div className="grid h-11 w-11 place-items-center rounded-full bg-[#0e3150] text-sm font-semibold text-white shadow-lg shadow-[#0e3150]/20">
                                    BE
                                </div>
                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.28em] text-[#ef6b57]">
                                        {copy.sectionLabel}
                                    </p>
                                    <h1 className="text-lg font-semibold text-[#16324f]">
                                        {appName}
                                    </h1>
                                </div>
                            </div>

                            <nav className="hidden items-center gap-6 text-sm font-medium text-[#30526d] lg:flex">
                                <a
                                    href="#destinations"
                                    className="transition hover:text-[#0f5ea8]"
                                >
                                    Destinations
                                </a>
                                <a
                                    href="#stays"
                                    className="transition hover:text-[#0f5ea8]"
                                >
                                    Stays
                                </a>
                                <a
                                    href="#guest-pulse"
                                    className="transition hover:text-[#0f5ea8]"
                                >
                                    Guest pulse
                                </a>
                            </nav>

                            <div className="flex items-center gap-3">
                                <div className="hidden rounded-full border border-[#d9e8f5] bg-[#f7fbff] px-4 py-2 text-sm text-[#58758e] md:flex">
                                    API live:{" "}
                                    <span className="ml-1 font-semibold text-[#0f5ea8]">
                                        {formatCompact(
                                            homeData.stats.approvedProperties,
                                        )}
                                    </span>
                                </div>
                                <a
                                    href="/admin/login"
                                    className="inline-flex items-center justify-center rounded-full bg-[#0f5ea8] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#0f5ea8]/20 transition hover:bg-[#124f89]"
                                >
                                    {copy.adminCta}
                                </a>
                            </div>
                        </div>
                    </header>

                    <section className="relative mt-8 grid gap-7 lg:grid-cols-[1.12fr_0.88fr] lg:items-start">
                        <div className="space-y-6">
                            <div className="max-w-190">
                                <div className="inline-flex items-center gap-2 rounded-full border border-[#ffe2bf] bg-[#fff6ea] px-4 py-2 text-xs font-semibold uppercase tracking-[0.26em] text-[#c96b17]">
                                    Tailwind CSS 4 storefront
                                </div>
                                <h2 className="mt-5 max-w-[13ch] text-[2.95rem] font-semibold leading-[0.96] tracking-tighter text-[#16324f] sm:text-[4.5rem]">
                                    {copy.heroLead}
                                </h2>
                                <p className="mt-5 max-w-155 text-lg leading-8 text-[#5f7d94]">
                                    {copy.heroBody}
                                </p>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                {[
                                    [
                                        "Approved stays",
                                        homeData.stats.approvedProperties,
                                    ],
                                    [
                                        "Featured now",
                                        homeData.stats.featuredStays,
                                    ],
                                    [
                                        "Active destinations",
                                        homeData.stats.activeDestinations,
                                    ],
                                    [
                                        "Verified reviews",
                                        homeData.stats.approvedReviews,
                                    ],
                                ].map(([label, value]) => (
                                    <div
                                        key={label}
                                        className="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-[0_14px_40px_rgba(14,49,80,0.07)] backdrop-blur"
                                    >
                                        <p className="text-sm font-medium text-[#6d889b]">
                                            {label}
                                        </p>
                                        <p className="mt-3 text-3xl font-semibold tracking-tighter text-[#16324f]">
                                            {formatCompact(value)}
                                        </p>
                                    </div>
                                ))}
                            </div>

                            <div className="rounded-4xl border border-white/75 bg-white/88 p-4 shadow-[0_24px_60px_rgba(14,49,80,0.11)] backdrop-blur sm:p-5">
                                <div className="grid gap-3 lg:grid-cols-[1.2fr_0.9fr_0.8fr_auto]">
                                    <label className="rounded-3xl border border-[#dfeaf4] bg-[#f8fbfe] px-4 py-4 text-sm text-[#6a8497]">
                                        <span className="block text-xs font-semibold uppercase tracking-[0.24em] text-[#7895ac]">
                                            Destination
                                        </span>
                                        <input
                                            value={selectedDestination}
                                            onChange={(event) =>
                                                setSelectedDestination(
                                                    event.target.value,
                                                )
                                            }
                                            placeholder="Phnom Penh, Siem Reap, Kampot"
                                            className="mt-2 w-full bg-transparent text-base font-semibold text-[#16324f] outline-none"
                                        />
                                    </label>

                                    <div className="grid grid-cols-2 gap-3">
                                        {["Check-in", "Check-out"].map(
                                            (label) => (
                                                <div
                                                    key={label}
                                                    className="rounded-3xl border border-[#dfeaf4] bg-[#f8fbfe] px-4 py-4 text-sm text-[#6a8497]"
                                                >
                                                    <span className="block text-xs font-semibold uppercase tracking-[0.24em] text-[#7895ac]">
                                                        {label}
                                                    </span>
                                                    <span className="mt-2 block text-base font-semibold text-[#16324f]">
                                                        Flexible
                                                    </span>
                                                </div>
                                            ),
                                        )}
                                    </div>

                                    <div className="rounded-3xl border border-[#dfeaf4] bg-[#f8fbfe] px-4 py-4 text-sm text-[#6a8497]">
                                        <span className="block text-xs font-semibold uppercase tracking-[0.24em] text-[#7895ac]">
                                            Guests
                                        </span>
                                        <span className="mt-2 block text-base font-semibold text-[#16324f]">
                                            2 adults · 1 room
                                        </span>
                                    </div>

                                    <button className="inline-flex min-h-19.5 items-center justify-center rounded-3xl bg-[#ef6b57] px-6 text-base font-semibold text-white shadow-[0_18px_30px_rgba(239,107,87,0.28)] transition hover:bg-[#de5a46]">
                                        {copy.searchButton}
                                    </button>
                                </div>

                                <div className="mt-4 flex flex-wrap gap-2">
                                    {safeDestinations
                                        .slice(0, 5)
                                        .map((destination) => {
                                            const active =
                                                destination.name ===
                                                selectedDestination;

                                            return (
                                                <button
                                                    key={destination.id}
                                                    type="button"
                                                    onClick={() =>
                                                        setSelectedDestination(
                                                            destination.name,
                                                        )
                                                    }
                                                    className={`rounded-full px-4 py-2 text-sm font-medium transition ${active ? "bg-[#0f5ea8] text-white shadow-lg shadow-[#0f5ea8]/20" : "bg-[#eef5fb] text-[#40637c] hover:bg-[#e3eef8]"}`}
                                                >
                                                    {destination.name}
                                                </button>
                                            );
                                        })}
                                </div>
                            </div>
                        </div>

                        <div className="grid gap-5">
                            <SurfaceImage
                                imageUrl={highlightDestination?.imageUrl}
                                gradient={destinationGradients[1]}
                                eyebrow="Popular right now"
                                title={
                                    highlightDestination?.name ||
                                    "Curated city breaks"
                                }
                                subtitle={
                                    highlightDestination
                                        ? `${highlightDestination.propertyCount} stays across ${highlightDestination.city || highlightDestination.country || "the network"}`
                                        : "Fresh inventory from your booking ERP, ready to spotlight."
                                }
                            />

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="rounded-[28px] border border-white/75 bg-[#0e3150] p-6 text-white shadow-[0_24px_55px_rgba(14,49,80,0.18)]">
                                    <p className="text-xs font-semibold uppercase tracking-[0.28em] text-white/65">
                                        Revenue-ready cards
                                    </p>
                                    <p className="mt-4 text-3xl font-semibold tracking-tighter">
                                        {highlightProperty
                                            ? formatPrice(
                                                  highlightProperty.startingPrice,
                                              )
                                            : "$128"}
                                    </p>
                                    <p className="mt-3 text-sm leading-6 text-white/72">
                                        Showcase minimum live room pricing from
                                        the backend API and keep the storefront
                                        aligned with admin updates.
                                    </p>
                                </div>

                                <SurfaceImage
                                    imageUrl={highlightProperty?.imageUrl}
                                    gradient={propertyGradients[0]}
                                    eyebrow={
                                        highlightProperty?.propertyType ||
                                        "Featured stay"
                                    }
                                    title={
                                        highlightProperty?.name ||
                                        "Design-led stay cards"
                                    }
                                    subtitle={
                                        highlightProperty?.location ||
                                        "Structured for destinations, ratings, and pricing highlights."
                                    }
                                    compact
                                />
                            </div>
                        </div>
                    </section>

                    <section id="destinations" className="mt-16">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p className="text-sm font-semibold uppercase tracking-[0.28em] text-[#ef6b57]">
                                    {copy.destinationLabel}
                                </p>
                                <h3 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#16324f] sm:text-4xl">
                                    Build destination discovery the way guests
                                    actually browse.
                                </h3>
                            </div>
                            <p className="max-w-110 text-sm leading-7 text-[#69859a]">
                                Popular markets, property counts, and storefront
                                imagery all come from your Laravel data layer,
                                so the homepage updates as inventory evolves.
                            </p>
                        </div>

                        <div className="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            {(status === "loading"
                                ? Array.from({ length: 6 }, (_, index) => ({
                                      id: `skeleton-${index}`,
                                  }))
                                : safeDestinations
                            ).map((destination, index) => {
                                if (status === "loading") {
                                    return (
                                        <div
                                            key={destination.id}
                                            className="h-60 animate-pulse rounded-[28px] bg-white/70"
                                        />
                                    );
                                }

                                return (
                                    <SurfaceImage
                                        key={destination.id}
                                        imageUrl={destination.imageUrl}
                                        gradient={
                                            destinationGradients[
                                                index %
                                                    destinationGradients.length
                                            ]
                                        }
                                        eyebrow={
                                            destination.country || "Destination"
                                        }
                                        title={destination.name}
                                        subtitle={`${destination.propertyCount} available stays${destination.city ? ` • ${destination.city}` : ""}`}
                                        compact={false}
                                    />
                                );
                            })}
                        </div>
                    </section>

                    <section id="stays" className="mt-18 lg:mt-24">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p className="text-sm font-semibold uppercase tracking-[0.28em] text-[#ef6b57]">
                                    {copy.featuredLabel}
                                </p>
                                <h3 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#16324f] sm:text-4xl">
                                    Agoda-style property cards, but from your
                                    own ERP catalog.
                                </h3>
                            </div>
                            <div className="rounded-full border border-[#d7e7f4] bg-white/80 px-4 py-2 text-sm text-[#5c7890] shadow-sm">
                                {homeData.featuredProperties.length ||
                                    safeProperties.length}{" "}
                                stays highlighted from API
                            </div>
                        </div>

                        <div className="mt-8 grid gap-5 xl:grid-cols-4 md:grid-cols-2">
                            {(status === "loading"
                                ? Array.from({ length: 4 }, (_, index) => ({
                                      id: `property-${index}`,
                                  }))
                                : safeProperties.slice(0, 8)
                            ).map((property, index) => {
                                if (status === "loading") {
                                    return (
                                        <div
                                            key={property.id}
                                            className="h-95 animate-pulse rounded-[30px] bg-white/70"
                                        />
                                    );
                                }

                                return (
                                    <article
                                        key={property.id}
                                        className="overflow-hidden rounded-[30px] border border-white/70 bg-white/90 shadow-[0_22px_60px_rgba(14,49,80,0.09)] backdrop-blur"
                                    >
                                        <div
                                            className={`relative h-60 ${property.imageUrl ? "bg-slate-700" : `bg-linear-to-br ${propertyGradients[index % propertyGradients.length]}`}`}
                                            style={
                                                property.imageUrl
                                                    ? {
                                                          backgroundImage: `linear-gradient(180deg, rgba(14,49,80,0.12), rgba(14,49,80,0.4)), url(${property.imageUrl})`,
                                                          backgroundSize:
                                                              "cover",
                                                          backgroundPosition:
                                                              "center",
                                                      }
                                                    : undefined
                                            }
                                        >
                                            <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.2),transparent_24%)]" />
                                            <div className="absolute left-4 top-4 flex gap-2">
                                                {property.isFeatured && (
                                                    <span className="rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-[#0f5ea8]">
                                                        Featured
                                                    </span>
                                                )}
                                                <span className="rounded-full bg-[#0e3150]/80 px-3 py-1 text-xs font-semibold text-white">
                                                    {property.propertyType}
                                                </span>
                                            </div>
                                        </div>

                                        <div className="space-y-4 p-5">
                                            <div className="space-y-2">
                                                <StarRow
                                                    value={property.starRating}
                                                />
                                                <h4 className="text-xl font-semibold leading-7 tracking-[-0.03em] text-[#16324f]">
                                                    {property.name}
                                                </h4>
                                                <p className="text-sm text-[#66839a]">
                                                    {property.location ||
                                                        property.address}
                                                </p>
                                            </div>

                                            <p className="text-sm leading-7 text-[#58748a]">
                                                {property.description ||
                                                    "Well-positioned inventory card with review, occupancy, and pricing highlights."}
                                            </p>

                                            <div className="flex items-end justify-between gap-4 border-t border-[#e7eef5] pt-4">
                                                <div>
                                                    <p className="text-xs font-semibold uppercase tracking-[0.24em] text-[#87a0b3]">
                                                        From
                                                    </p>
                                                    <p className="mt-1 text-2xl font-semibold tracking-[-0.04em] text-[#0e3150]">
                                                        {formatPrice(
                                                            property.startingPrice,
                                                        )}
                                                    </p>
                                                    <p className="mt-1 text-xs text-[#7d97ab]">
                                                        per night • up to{" "}
                                                        {property.maxOccupancy ||
                                                            2}{" "}
                                                        guests
                                                    </p>
                                                </div>

                                                <div className="rounded-[20px] bg-[#f5f9fd] px-3 py-2 text-right">
                                                    <p className="text-lg font-semibold text-[#16324f]">
                                                        {property.reviewScore
                                                            ? property.reviewScore.toFixed(
                                                                  1,
                                                              )
                                                            : "New"}
                                                    </p>
                                                    <p className="text-xs text-[#70899d]">
                                                        {ratingLabel(
                                                            property.reviewScore,
                                                        )}{" "}
                                                        · {property.reviewCount}{" "}
                                                        reviews
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>
                    </section>

                    <section className="mt-18 grid gap-5 lg:mt-24 lg:grid-cols-[0.85fr_1.15fr]">
                        <div className="rounded-[34px] bg-[#0e3150] p-8 text-white shadow-[0_28px_70px_rgba(14,49,80,0.2)]">
                            <p className="text-sm font-semibold uppercase tracking-[0.28em] text-white/65">
                                API-backed positioning
                            </p>
                            <h3 className="mt-4 text-3xl font-semibold tracking-[-0.04em]">
                                A modern storefront without leaving Laravel.
                            </h3>
                            <p className="mt-4 text-sm leading-7 text-white/76">
                                This keeps the current Laravel + Inertia React
                                stack, but reshapes the UI to feel closer to a
                                modern OTA landing page while reading live
                                backend data through JSON.
                            </p>
                            <div className="mt-7 space-y-3">
                                {[
                                    "Hero search surface designed for hotel discovery",
                                    "Destination and property cards fed from backend aggregates",
                                    "Chunk splitting to keep Vite warnings under control",
                                ].map((item) => (
                                    <div
                                        key={item}
                                        className="flex items-start gap-3 rounded-[22px] bg-white/10 px-4 py-3 text-sm text-white/86"
                                    >
                                        <span className="mt-1 h-2.5 w-2.5 rounded-full bg-[#f7b955]" />
                                        <span>{item}</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-[34px] border border-white/70 bg-white/88 p-7 shadow-[0_24px_60px_rgba(14,49,80,0.08)] backdrop-blur">
                            <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.28em] text-[#ef6b57]">
                                        {copy.typesLabel}
                                    </p>
                                    <h3 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#16324f]">
                                        Merchandise inventory by stay intent.
                                    </h3>
                                </div>
                            </div>

                            <div className="mt-7 grid gap-4 sm:grid-cols-2">
                                {homeData.propertyTypes.map((type, index) => (
                                    <div
                                        key={type.id}
                                        className={`rounded-[26px] bg-linear-to-br ${propertyGradients[index % propertyGradients.length]} p-px`}
                                    >
                                        <div className="rounded-[25px] bg-white/92 p-5 backdrop-blur">
                                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-[#7c96aa]">
                                                Category
                                            </p>
                                            <h4 className="mt-3 text-2xl font-semibold tracking-[-0.04em] text-[#16324f]">
                                                {type.name}
                                            </h4>
                                            <p className="mt-2 text-sm text-[#5d7c93]">
                                                {formatCompact(
                                                    type.propertyCount,
                                                )}{" "}
                                                active listings ready to sell.
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    <section id="guest-pulse" className="mt-18 lg:mt-24">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p className="text-sm font-semibold uppercase tracking-[0.28em] text-[#ef6b57]">
                                    {copy.reviewsLabel}
                                </p>
                                <h3 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[#16324f] sm:text-4xl">
                                    Let recent guest feedback sell the
                                    experience.
                                </h3>
                            </div>
                        </div>

                        <div className="mt-8 grid gap-5 lg:grid-cols-3">
                            {(status === "error" ? [] : safeReviews).map(
                                (review, index) => (
                                    <article
                                        key={review.id}
                                        className="rounded-[30px] border border-white/70 bg-white/90 p-6 shadow-[0_22px_55px_rgba(14,49,80,0.08)]"
                                    >
                                        <div className="flex items-center justify-between gap-4">
                                            <div>
                                                <p className="text-xs font-semibold uppercase tracking-[0.24em] text-[#7e9aaf]">
                                                    {review.guestName}
                                                </p>
                                                <h4 className="mt-2 text-xl font-semibold tracking-[-0.03em] text-[#16324f]">
                                                    {review.title}
                                                </h4>
                                            </div>
                                            <div className="rounded-[18px] bg-[#0f5ea8] px-3 py-2 text-sm font-semibold text-white">
                                                {review.rating.toFixed(1)}
                                            </div>
                                        </div>
                                        <p className="mt-4 text-sm leading-7 text-[#607d93]">
                                            “{review.comment}”
                                        </p>
                                        <div className="mt-6 flex items-center justify-between text-sm text-[#7792a7]">
                                            <span>{review.propertyName}</span>
                                            <span>
                                                {review.city || review.date}
                                            </span>
                                        </div>
                                    </article>
                                ),
                            )}

                            {status === "error" && (
                                <div className="rounded-[30px] border border-dashed border-[#c8ddec] bg-white/80 p-8 text-sm leading-7 text-[#5c7a92] lg:col-span-3">
                                    Homepage API data could not be loaded. The
                                    layout is in place, but check the backend
                                    endpoint at {apiUrl}.
                                </div>
                            )}
                        </div>
                    </section>
                </div>
            </div>
        </>
    );
}
