import { Head } from "@inertiajs/react";
import {
    startTransition,
    useEffect,
    useMemo,
    useRef,
    useState,
} from "react";

const initialData = {
    hero: {
        headline: "See the world for less",
        subheadline:
            "Hotels, homes, activities and airport transfers — all in one booking flow.",
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
    promotions: [],
};

const fallbackDestinations = [
    {
        id: "demo-phnom-penh",
        name: "Phnom Penh",
        city: "Phnom Penh",
        country: "Cambodia",
        propertyCount: 1240,
        imageUrl: null,
    },
    {
        id: "demo-siem-reap",
        name: "Siem Reap",
        city: "Siem Reap",
        country: "Cambodia",
        propertyCount: 980,
        imageUrl: null,
    },
    {
        id: "demo-kampot",
        name: "Kampot",
        city: "Kampot",
        country: "Cambodia",
        propertyCount: 312,
        imageUrl: null,
    },
    {
        id: "demo-bangkok",
        name: "Bangkok",
        city: "Bangkok",
        country: "Thailand",
        propertyCount: 12048,
        imageUrl: null,
    },
    {
        id: "demo-da-nang",
        name: "Da Nang",
        city: "Da Nang",
        country: "Vietnam",
        propertyCount: 4870,
        imageUrl: null,
    },
    {
        id: "demo-singapore",
        name: "Singapore",
        city: "Singapore",
        country: "Singapore",
        propertyCount: 2154,
        imageUrl: null,
    },
    {
        id: "demo-bali",
        name: "Bali",
        city: "Bali",
        country: "Indonesia",
        propertyCount: 32908,
        imageUrl: null,
    },
    {
        id: "demo-kuala-lumpur",
        name: "Kuala Lumpur",
        city: "Kuala Lumpur",
        country: "Malaysia",
        propertyCount: 19902,
        imageUrl: null,
    },
];

const fallbackProperties = [
    {
        id: "demo-riverside",
        name: "Riverside Atelier Hotel",
        propertyType: "Boutique Hotel",
        description:
            "A polished riverside stay with skyline rooms and curated breakfast.",
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
            "Long-stay friendly units in BKK1 with kitchenettes and roof terrace.",
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
            "Family resort minutes from Angkor Wat, with pool, spa and shuttle.",
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
            "Riverside hostel with rooftop bar, perfect for solo travellers.",
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
    {
        id: "demo-tonle",
        name: "Tonle Skyline Residences",
        propertyType: "Apartment",
        description:
            "Two-bedroom apartments overlooking the Mekong with full kitchens.",
        location: "Phnom Penh, Cambodia",
        address: "Tonle Bassac, Phnom Penh",
        starRating: 4.4,
        reviewScore: 8.6,
        reviewCount: 156,
        startingPrice: 118,
        maxOccupancy: 5,
        isFeatured: false,
        imageUrl: null,
    },
];

const fallbackPromotions = [
    {
        id: "promo-spring",
        code: "SPRING25",
        name: "Spring Escape — 25% off select stays",
        type: "percentage",
        discountValue: 25,
        discountLabel: "25% off",
        startDate: null,
        endDate: "31 May 2026",
        minNights: 2,
        propertyName: null,
        city: "Across Southeast Asia",
        imageUrl: null,
    },
    {
        id: "promo-flash",
        code: "FLASH48",
        name: "Flash Deal — 48 hours of family savings",
        type: "fixed",
        discountValue: 50,
        discountLabel: "$50 off",
        startDate: null,
        endDate: null,
        minNights: 3,
        propertyName: "Angkor Horizon Retreat",
        city: "Siem Reap",
        imageUrl: null,
    },
    {
        id: "promo-stay-longer",
        code: "STAYLONG",
        name: "Stay 4 nights, get 1 free",
        type: "free_night",
        discountValue: 1,
        discountLabel: "Free night",
        startDate: null,
        endDate: null,
        minNights: 4,
        propertyName: "Sunrise Courtyard Suites",
        city: "Phnom Penh",
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
    "from-[#0f5ea8] via-[#1d7bd1] to-[#4ca6ef]",
    "from-[#ff6f3c] via-[#ff8b4d] to-[#ffc36e]",
    "from-[#003049] via-[#19608d] to-[#5dbdf0]",
    "from-[#4d148c] via-[#7b2cbf] to-[#c77dff]",
    "from-[#005f73] via-[#0a9396] to-[#94d2bd]",
    "from-[#7f5539] via-[#b56576] to-[#e56b6f]",
    "from-[#1b4332] via-[#2d6a4f] to-[#74c69d]",
    "from-[#003566] via-[#0353a4] to-[#62b6cb]",
];

const promotionGradients = [
    "from-[#0058a3] via-[#1273c5] to-[#2596e0]",
    "from-[#ff5a36] via-[#ff7b3a] to-[#ffb56a]",
    "from-[#5b2cad] via-[#8b3ce8] to-[#c084fc]",
    "from-[#0c6e6e] via-[#13a3a3] to-[#5fd7d7]",
    "from-[#a4133c] via-[#c9184a] to-[#ff4d6d]",
    "from-[#1e3a8a] via-[#2563eb] to-[#60a5fa]",
];

function IconHotels(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M3 21V8l4-3 4 3v13" />
            <path d="M11 21V11l5-3 5 3v10" />
            <path d="M3 21h18" />
            <path d="M6 12h.01M6 15h.01M14 14h.01M14 17h.01M18 14h.01M18 17h.01" />
        </svg>
    );
}

function IconHome(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M3 11.5 12 4l9 7.5" />
            <path d="M5 10v10h14V10" />
            <path d="M10 20v-5h4v5" />
        </svg>
    );
}

function IconLongStay(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <rect x="3.5" y="5" width="17" height="15" rx="2" />
            <path d="M3.5 9.5h17" />
            <path d="M8 3.5v3M16 3.5v3" />
            <text x="12" y="17" textAnchor="middle" fontSize="6" fontWeight="700" stroke="none" fill="currentColor">7+</text>
        </svg>
    );
}

function IconCar(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M4 14l2-5.5A2 2 0 0 1 7.9 7h8.2a2 2 0 0 1 1.9 1.5L20 14" />
            <rect x="3" y="14" width="18" height="5" rx="1.5" />
            <circle cx="7.5" cy="19" r="1.5" fill="currentColor" stroke="none" />
            <circle cx="16.5" cy="19" r="1.5" fill="currentColor" stroke="none" />
        </svg>
    );
}

function IconSearch(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
        </svg>
    );
}

function IconCalendarIn(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <rect x="3.5" y="5" width="17" height="15" rx="2" />
            <path d="M3.5 9.5h17" />
            <path d="M8 3.5v3M16 3.5v3" />
            <path d="M9 14h4" />
            <path d="m11 12 2 2-2 2" />
        </svg>
    );
}

function IconCalendarOut(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <rect x="3.5" y="5" width="17" height="15" rx="2" />
            <path d="M3.5 9.5h17" />
            <path d="M8 3.5v3M16 3.5v3" />
            <path d="M15 14h-4" />
            <path d="m13 12-2 2 2 2" />
        </svg>
    );
}

function IconUsers(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <circle cx="9" cy="8" r="3.5" />
            <path d="M3 19c0-3 3-5 6-5s6 2 6 5" />
            <circle cx="16.5" cy="9" r="2.5" />
            <path d="M16.5 14c2.6 0 4.5 1.6 4.5 4" />
        </svg>
    );
}

function IconChevronDown(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="m6 9 6 6 6-6" />
        </svg>
    );
}

function IconPlane(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M3 14.5 21 8l-3 12-4-4-4 2-2-2.5z" />
        </svg>
    );
}

function IconPin(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z" />
            <circle cx="12" cy="9" r="2.5" />
        </svg>
    );
}

function IconClock(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 8v4.5l3 1.5" />
        </svg>
    );
}

function IconSwap(props) {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...props}>
            <path d="M4 8h13l-3-3" />
            <path d="M20 16H7l3 3" />
        </svg>
    );
}

const SEARCH_TABS = [
    { id: "hotels", Icon: IconHotels },
    { id: "homes", Icon: IconHome },
    { id: "longstays", Icon: IconLongStay },
    { id: "transfer", Icon: IconCar },
];

const COPY = {
    en: {
        tagline: "Bundle and save!",
        navHotels: "Hotels & Homes",
        navTransport: "Transport",
        navActivities: "Activities",
        navDeals: "Coupons & Deals",
        navAdmin: "Admin",
        signIn: "Sign in",
        createAccount: "Create account",
        listYourPlace: "List your place",
        heroHeadline: "See the world for less",
        heroSubheadline:
            "Hotels, homes, activities and airport transfers — all in one booking flow.",
        searchTabs: {
            hotels: "Hotels",
            homes: "Homes & Apts",
            longstays: "Long stays",
            transfer: "Airport transfer",
        },
        stayTypeOvernight: "Overnight Stays",
        stayTypeDayUse: "Day Use Stays",
        transferFromAirport: "From airport",
        transferToAirport: "To airport",
        destinationPlaceholder: "Enter a destination or property",
        pickupAirportPlaceholder: "Pick-up airport",
        pickupLocationPlaceholder: "Pick-up location",
        destinationLocationPlaceholder: "Destination location",
        pickupDateLabel: "Pick-up date",
        passengersLabel: (n) => `${n} passenger${n === 1 ? "" : "s"}`,
        guestsAdultsOnly: (a) => `${a} adult${a === 1 ? "" : "s"}`,
        guestsRoomsLine: (r) => `${r} room${r === 1 ? "" : "s"}`,
        guestsSummary: (a, c, r) =>
            `${a} adult${a === 1 ? "" : "s"}${c ? `, ${c} child${c === 1 ? "" : "ren"}` : ""} · ${r} room${r === 1 ? "" : "s"}`,
        adultsLabel: "Adults",
        childrenLabel: "Children",
        roomsLabel: "Rooms",
        passengersOnlyLabel: "Passengers",
        guestsApply: "Done",
        searchCta: "SEARCH",
        topDestinations: "Top destinations",
        featuredStays: "Stays we think you'll love",
        promotions: "Accommodation promotions",
        propertyTypes: "Browse by property type",
        reviews: "What guests are saying",
        viewAll: "View all",
        bookNow: "Book now",
        appHeading: "Save 10% on your 1st app booking!",
        appBody: "Just scan the QR code for instant savings.",
        footerHelp: "Help",
        footerCompany: "Company",
        footerPartners: "Partner with us",
        footerApp: "Get the app",
        footerDestinations: "Destinations",
        footerCopyright:
            "© 2005–2026 Booking ERP demo storefront. All rights reserved.",
        searchToast: "Public search results page is coming soon.",
        per: "per night",
        from: "from",
        rooms: "rooms",
    },
    km: {
        tagline: "ការផ្តល់ជូនពិសេស!",
        navHotels: "សណ្ឋាគារ និងផ្ទះ",
        navTransport: "ការដឹកជញ្ជូន",
        navActivities: "សកម្មភាព",
        navDeals: "ការផ្តល់ជូន",
        navAdmin: "ផ្ទាំងគ្រប់គ្រង",
        signIn: "ចូលគណនី",
        createAccount: "បង្កើតគណនី",
        listYourPlace: "ដាក់បង្ហាញកន្លែងរបស់អ្នក",
        heroHeadline: "រកការធ្វើដំណើរក្នុងតម្លៃសមរម្យ",
        heroSubheadline:
            "សណ្ឋាគារ ផ្ទះ សកម្មភាព និងសេវាដឹកជញ្ជូនអាកាសយានដ្ឋាន — ក្នុងច្រកការកក់តែមួយ។",
        searchTabs: {
            hotels: "សណ្ឋាគារ",
            homes: "ផ្ទះ និងបន្ទប់ជួល",
            longstays: "ស្នាក់រយៈពេលវែង",
            transfer: "ដឹកជញ្ជូនអាកាសយានដ្ឋាន",
        },
        stayTypeOvernight: "ស្នាក់មួយយប់",
        stayTypeDayUse: "ប្រើតែពេលថ្ងៃ",
        transferFromAirport: "ពីអាកាសយានដ្ឋាន",
        transferToAirport: "ទៅអាកាសយានដ្ឋាន",
        destinationPlaceholder: "បញ្ចូលគោលដៅ ឬឈ្មោះអចលនទ្រព្យ",
        pickupAirportPlaceholder: "អាកាសយានដ្ឋានទទួល",
        pickupLocationPlaceholder: "ទីតាំងទទួល",
        destinationLocationPlaceholder: "ទីតាំងគោលដៅ",
        pickupDateLabel: "ថ្ងៃទទួល",
        passengersLabel: (n) => `${n} អ្នកដំណើរ`,
        guestsAdultsOnly: (a) => `${a} មនុស្សពេញវ័យ`,
        guestsRoomsLine: (r) => `${r} បន្ទប់`,
        guestsSummary: (a, c, r) =>
            `${a} មនុស្សពេញវ័យ${c ? `, ${c} កុមារ` : ""} · ${r} បន្ទប់`,
        adultsLabel: "មនុស្សពេញវ័យ",
        childrenLabel: "កុមារ",
        roomsLabel: "បន្ទប់",
        passengersOnlyLabel: "អ្នកដំណើរ",
        guestsApply: "យល់ព្រម",
        searchCta: "ស្វែងរក",
        topDestinations: "គោលដៅពេញនិយម",
        featuredStays: "កន្លែងស្នាក់នៅណែនាំ",
        promotions: "ការផ្តល់ជូនពិសេស",
        propertyTypes: "តាមប្រភេទអចលនទ្រព្យ",
        reviews: "មតិពីភ្ញៀវ",
        viewAll: "មើលទាំងអស់",
        bookNow: "កក់ឥឡូវនេះ",
        appheading: "ទាញយកកម្មវិធី",
        appHeading: "សន្សំ 10% លើការកក់លើកដំបូងតាមកម្មវិធី!",
        appBody: "ស្កេន QR ខាងក្រោម ដើម្បីចាប់ផ្តើម។",
        footerHelp: "ជំនួយ",
        footerCompany: "ក្រុមហ៊ុន",
        footerPartners: "ភាគី",
        footerApp: "ទាញយកកម្មវិធី",
        footerDestinations: "គោលដៅ",
        footerCopyright:
            "© 2005–2026 Booking ERP demo storefront ។ រក្សាសិទ្ធិទាំងអស់។",
        searchToast: "ទំព័រលទ្ធផលស្វែងរកនឹងបង្ហាញនៅពេលឆាប់ៗ។",
        per: "ក្នុងមួយយប់",
        from: "ចាប់ពី",
        rooms: "បន្ទប់",
    },
};

function formatCompact(value) {
    if (!Number.isFinite(value)) return "0";
    return new Intl.NumberFormat("en", {
        notation: "compact",
        maximumFractionDigits: 1,
    }).format(value);
}

function formatPrice(amount) {
    if (amount === null || amount === undefined) {
        return null;
    }
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        maximumFractionDigits: 0,
    }).format(amount);
}

function todayPlus(days = 0) {
    const d = new Date();
    d.setDate(d.getDate() + days);
    const iso = d.toISOString().slice(0, 10);
    return iso;
}

function formatDateDisplay(iso, locale) {
    if (!iso) return "—";
    try {
        const d = new Date(iso);
        const formatter = new Intl.DateTimeFormat(
            locale === "km" ? "km-KH" : "en-GB",
            {
                day: "2-digit",
                month: "short",
                year: "numeric",
                weekday: "long",
            },
        );
        const parts = formatter.formatToParts(d);
        const lookup = parts.reduce((acc, part) => {
            if (part.type !== "literal") acc[part.type] = part.value;
            return acc;
        }, {});
        return `${lookup.day} ${lookup.month} ${lookup.year}|${lookup.weekday}`;
    } catch {
        return iso;
    }
}

function splitDateDisplay(value) {
    if (!value || typeof value !== "string") return { main: "—", sub: "" };
    const [main, sub = ""] = value.split("|");
    return { main, sub };
}

function ratingLabel(score, locale) {
    if (locale === "km") {
        if (score >= 9) return "ល្អឥតខ្ចោះ";
        if (score >= 8) return "ល្អណាស់";
        if (score >= 7) return "ល្អ";
        if (score > 0) return "មធ្យម";
        return "ថ្មី";
    }
    if (score >= 9) return "Exceptional";
    if (score >= 8) return "Excellent";
    if (score >= 7) return "Very good";
    if (score > 0) return "Good";
    return "New";
}

function Carousel({ children, ariaLabel }) {
    const trackRef = useRef(null);

    const scrollByAmount = (direction) => {
        const node = trackRef.current;
        if (!node) return;
        const amount = node.clientWidth * 0.85 * direction;
        node.scrollBy({ left: amount, behavior: "smooth" });
    };

    return (
        <div className="relative group" aria-label={ariaLabel}>
            <div
                ref={trackRef}
                className="flex gap-4 overflow-x-auto scroll-smooth pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden snap-x snap-mandatory"
            >
                {children}
            </div>
            <button
                type="button"
                aria-label="Scroll previous"
                onClick={() => scrollByAmount(-1)}
                className="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1/2 hidden h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg shadow-slate-900/10 transition hover:bg-slate-50 lg:flex"
            >
                <span aria-hidden="true">‹</span>
            </button>
            <button
                type="button"
                aria-label="Scroll next"
                onClick={() => scrollByAmount(1)}
                className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 hidden h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-lg shadow-slate-900/10 transition hover:bg-slate-50 lg:flex"
            >
                <span aria-hidden="true">›</span>
            </button>
        </div>
    );
}

function DestinationCard({ destination, gradient, copy }) {
    const style = destination.imageUrl
        ? {
              backgroundImage: `linear-gradient(180deg, rgba(15,23,42,0) 45%, rgba(15,23,42,0.55) 95%), url(${destination.imageUrl})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
          }
        : undefined;

    return (
        <a
            href="#"
            className={`group relative block w-56 shrink-0 snap-start overflow-hidden rounded-2xl text-white shadow-sm transition hover:shadow-lg sm:w-64 ${destination.imageUrl ? "bg-slate-700" : `bg-gradient-to-br ${gradient}`}`}
            style={style}
        >
            <div className="aspect-[4/5]">
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_45%)] transition group-hover:bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.32),transparent_50%)]" />
                <div className="relative flex h-full flex-col justify-end p-5">
                    <h3 className="text-xl font-semibold leading-tight">
                        {destination.name}
                    </h3>
                    <p className="mt-1 text-sm text-white/85">
                        {formatCompact(destination.propertyCount)}{" "}
                        {copy.rooms}
                    </p>
                </div>
            </div>
        </a>
    );
}

function PropertyCard({ property, copy, locale }) {
    const style = property.imageUrl
        ? {
              backgroundImage: `url(${property.imageUrl})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
          }
        : undefined;

    const formatted = formatPrice(property.startingPrice);

    return (
        <article className="flex w-72 shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-xl sm:w-80">
            <div
                className={`relative aspect-[4/3] ${property.imageUrl ? "bg-slate-200" : "bg-gradient-to-br from-[#0f5ea8] via-[#1d7bd1] to-[#4ca6ef]"}`}
                style={style}
            >
                {property.isFeatured ? (
                    <span className="absolute left-3 top-3 rounded-full bg-[#ef6b57] px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white shadow">
                        Featured
                    </span>
                ) : null}
            </div>
            <div className="flex flex-1 flex-col gap-3 p-4">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-wider text-[#0f5ea8]">
                        {property.propertyType}
                    </p>
                    <h3 className="mt-1 line-clamp-2 text-base font-semibold leading-snug text-slate-900">
                        {property.name}
                    </h3>
                    <p className="mt-1 line-clamp-1 text-sm text-slate-500">
                        {property.location || property.address}
                    </p>
                </div>

                <div className="flex items-center gap-2 text-sm">
                    <span className="inline-flex items-center rounded-md bg-[#0f5ea8] px-2 py-1 text-xs font-bold text-white">
                        {(property.reviewScore || 0).toFixed(1)}
                    </span>
                    <span className="font-semibold text-slate-800">
                        {ratingLabel(property.reviewScore || 0, locale)}
                    </span>
                    <span className="text-slate-500">
                        ({formatCompact(property.reviewCount || 0)})
                    </span>
                </div>

                <div className="mt-auto flex items-end justify-between border-t border-slate-100 pt-3">
                    <div>
                        <p className="text-xs text-slate-500">{copy.from}</p>
                        <p className="text-lg font-bold text-slate-900">
                            {formatted || "—"}
                        </p>
                        <p className="text-xs text-slate-500">{copy.per}</p>
                    </div>
                    <a
                        href="#"
                        className="inline-flex items-center justify-center rounded-md bg-[#0f5ea8] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0a4a85]"
                    >
                        {copy.bookNow}
                    </a>
                </div>
            </div>
        </article>
    );
}

function PromotionCard({ promo, gradient, copy }) {
    const style = promo.imageUrl
        ? {
              backgroundImage: `linear-gradient(120deg, rgba(15,23,42,0.55), rgba(15,23,42,0.2)), url(${promo.imageUrl})`,
              backgroundSize: "cover",
              backgroundPosition: "center",
          }
        : undefined;

    return (
        <a
            href="#"
            className={`relative flex w-80 shrink-0 snap-start overflow-hidden rounded-2xl text-white shadow-sm transition hover:shadow-xl sm:w-96 ${promo.imageUrl ? "bg-slate-700" : `bg-gradient-to-br ${gradient}`}`}
            style={style}
        >
            <div className="relative flex h-48 w-full flex-col justify-between p-5 sm:h-52">
                <div className="flex items-center justify-between">
                    <span className="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur">
                        {promo.code}
                    </span>
                    <span className="inline-flex items-center rounded-md bg-[#ef6b57] px-2 py-1 text-xs font-bold uppercase">
                        {promo.discountLabel}
                    </span>
                </div>
                <div>
                    <h3 className="text-lg font-semibold leading-snug sm:text-xl">
                        {promo.name}
                    </h3>
                    <p className="mt-1 text-sm text-white/85">
                        {[
                            promo.propertyName,
                            promo.city,
                            promo.endDate ? `Ends ${promo.endDate}` : null,
                            promo.minNights > 0
                                ? `Min ${promo.minNights} nights`
                                : null,
                        ]
                            .filter(Boolean)
                            .join(" · ")}
                    </p>
                    <span className="mt-3 inline-block text-sm font-semibold underline-offset-2 hover:underline">
                        {copy.bookNow} →
                    </span>
                </div>
            </div>
        </a>
    );
}

function GuestsPopover({ values, onChange, copy, onClose, rows }) {
    const set = (key, delta) => {
        const next = { ...values };
        const definition = rows.find((r) => r.key === key);
        const min = definition?.min ?? 0;
        next[key] = Math.max(min, (next[key] || 0) + delta);
        onChange(next);
    };

    return (
        <div className="absolute right-0 left-0 top-full z-30 mt-2 rounded-xl border border-slate-200 bg-white p-4 text-slate-800 shadow-2xl sm:left-auto sm:w-72">
            {rows.map((row) => (
                <div
                    key={row.key}
                    className="flex items-center justify-between gap-3 py-2"
                >
                    <span className="text-sm font-medium">{row.label}</span>
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            aria-label={`Decrease ${row.label}`}
                            onClick={() => set(row.key, -1)}
                            disabled={values[row.key] <= (row.min ?? 0)}
                            className="grid h-8 w-8 place-items-center rounded-full border border-slate-300 text-lg leading-none text-slate-700 transition hover:border-[#0f5ea8] hover:text-[#0f5ea8] disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            –
                        </button>
                        <span className="w-6 text-center font-semibold">
                            {values[row.key]}
                        </span>
                        <button
                            type="button"
                            aria-label={`Increase ${row.label}`}
                            onClick={() => set(row.key, 1)}
                            className="grid h-8 w-8 place-items-center rounded-full border border-slate-300 text-lg leading-none text-slate-700 transition hover:border-[#0f5ea8] hover:text-[#0f5ea8]"
                        >
                            +
                        </button>
                    </div>
                </div>
            ))}
            <button
                type="button"
                onClick={onClose}
                className="mt-3 w-full rounded-md bg-[#0f5ea8] py-2 text-sm font-semibold text-white transition hover:bg-[#0a4a85]"
            >
                {copy.guestsApply}
            </button>
        </div>
    );
}

function DateField({ Icon, value, min, onChange, main, sub }) {
    const hiddenRef = useRef(null);
    return (
        <div
            role="button"
            tabIndex={0}
            onKeyDown={(event) => {
                if (event.key === "Enter" || event.key === " ") {
                    event.preventDefault();
                    hiddenRef.current?.showPicker?.();
                }
            }}
            onClick={() => hiddenRef.current?.showPicker?.()}
            className="flex cursor-pointer items-center gap-3 rounded-xl bg-white px-4 py-3 transition hover:ring-1 hover:ring-slate-300"
        >
            <Icon className="h-5 w-5 shrink-0 text-slate-500" />
            <span className="flex-1 truncate">
                <span className="block text-sm font-semibold text-slate-900">
                    {main || value}
                </span>
                {sub ? (
                    <span className="block text-xs font-medium text-slate-500">
                        {sub}
                    </span>
                ) : null}
            </span>
            <input
                ref={hiddenRef}
                type="date"
                value={value}
                min={min}
                onChange={(event) => onChange(event.target.value)}
                className="sr-only"
                tabIndex={-1}
                aria-hidden="true"
            />
        </div>
    );
}

export default function Welcome({
    appName = "Booking ERP",
    locale = "en",
    apiUrl,
}) {
    const [homeData, setHomeData] = useState(initialData);
    const [, setStatus] = useState("loading");
    const [activeTab, setActiveTab] = useState("hotels");
    const [stayType, setStayType] = useState("overnight");
    const [transferDirection, setTransferDirection] = useState("from");
    const [destinationQuery, setDestinationQuery] = useState("");
    const [pickupAirport, setPickupAirport] = useState("");
    const [destinationLocation, setDestinationLocation] = useState("");
    const [pickupTime, setPickupTime] = useState("12:00");
    const [checkIn, setCheckIn] = useState(todayPlus(8));
    const [checkOut, setCheckOut] = useState(todayPlus(15));
    const [guests, setGuests] = useState({
        adults: 1,
        children: 0,
        rooms: 1,
        passengers: 1,
    });
    const [guestsOpen, setGuestsOpen] = useState(false);
    const [toast, setToast] = useState(null);

    const copy = COPY[locale] || COPY.en;

    useEffect(() => {
        let cancelled = false;

        async function loadHomepage() {
            setStatus("loading");
            try {
                const response = await fetch(apiUrl, {
                    headers: { Accept: "application/json" },
                    cache: "no-store",
                });
                if (!response.ok) throw new Error("Failed to load homepage");

                const payload = await response.json();
                if (cancelled) return;

                startTransition(() => {
                    setHomeData({
                        ...initialData,
                        ...payload,
                    });
                });
                setStatus("ready");
            } catch (error) {
                if (!cancelled) setStatus("error");
            }
        }

        if (apiUrl) loadHomepage();

        return () => {
            cancelled = true;
        };
    }, [apiUrl]);

    const safeDestinations = homeData.destinations.length
        ? homeData.destinations
        : fallbackDestinations;
    const safeProperties = homeData.featuredProperties.length
        ? homeData.featuredProperties
        : fallbackProperties;
    const safePromotions = homeData.promotions?.length
        ? homeData.promotions
        : fallbackPromotions;
    const safeReviews = homeData.reviews.length
        ? homeData.reviews
        : fallbackReviews;
    const safePropertyTypes = homeData.propertyTypes.length
        ? homeData.propertyTypes
        : [
              { id: "pt-hotel", name: "Hotel", propertyCount: 12 },
              { id: "pt-apartment", name: "Apartment", propertyCount: 8 },
              { id: "pt-resort", name: "Resort", propertyCount: 4 },
              { id: "pt-hostel", name: "Hostel", propertyCount: 3 },
          ];

    const hasRooms = activeTab !== "transfer";
    const isTransfer = activeTab === "transfer";
    const formattedCheckIn = useMemo(
        () => formatDateDisplay(checkIn, locale),
        [checkIn, locale],
    );
    const formattedCheckOut = useMemo(
        () => formatDateDisplay(checkOut, locale),
        [checkOut, locale],
    );
    const checkInParts = useMemo(
        () => splitDateDisplay(formattedCheckIn),
        [formattedCheckIn],
    );
    const checkOutParts = useMemo(
        () => splitDateDisplay(formattedCheckOut),
        [formattedCheckOut],
    );

    const handleSwapTransferEnds = () => {
        setPickupAirport(destinationLocation);
        setDestinationLocation(pickupAirport);
    };

    const handleSearchSubmit = (event) => {
        event.preventDefault();
        setToast(copy.searchToast);
        setTimeout(() => setToast(null), 3200);
    };

    const switchLocale = async (next) => {
        if (next === locale) return;
        try {
            const tokenMeta = document.querySelector(
                'meta[name="csrf-token"]',
            );
            const csrf = tokenMeta ? tokenMeta.getAttribute("content") : "";
            await fetch("/lang/switch", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrf,
                },
                body: JSON.stringify({ locale: next }),
            });
            window.location.reload();
        } catch {
            window.location.reload();
        }
    };

    return (
        <>
            <Head title={`${appName} — ${copy.heroHeadline}`} />

            <div className="min-h-screen bg-white text-slate-800">
                {/* HEADER */}
                <header className="sticky top-0 z-40 border-b border-slate-200 bg-white">
                    <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                        <div className="flex items-center gap-2 sm:gap-6">
                            <a
                                href="/"
                                aria-label={appName}
                                className="flex items-center gap-2"
                            >
                                <span className="grid h-9 w-9 place-items-center rounded-full bg-[#0f5ea8] text-sm font-bold text-white">
                                    BE
                                </span>
                                <span className="hidden text-base font-bold tracking-tight text-slate-900 sm:inline">
                                    {appName}
                                </span>
                            </a>

                            <nav className="hidden items-center gap-1 text-sm font-medium text-slate-700 lg:flex">
                                <a
                                    href="#stays"
                                    className="rounded-md px-3 py-2 transition hover:bg-slate-50"
                                >
                                    {copy.navHotels}
                                </a>
                                <div className="relative">
                                    <span className="pointer-events-none absolute -top-2 right-1 z-10 rounded-md bg-[#ef6b57] px-1.5 py-0.5 text-[10px] font-bold uppercase leading-none text-white shadow-sm">
                                        New
                                    </span>
                                    <a
                                        href="#destinations"
                                        className="rounded-md px-3 py-2 transition hover:bg-slate-50"
                                    >
                                        {copy.navTransport}
                                    </a>
                                </div>
                                <a
                                    href="#types"
                                    className="rounded-md px-3 py-2 transition hover:bg-slate-50"
                                >
                                    {copy.navActivities}
                                </a>
                                <div className="relative">
                                    <span className="pointer-events-none absolute -top-2 right-1 z-10 rounded-md bg-[#ef6b57] px-1.5 py-0.5 text-[10px] font-bold uppercase leading-none text-white shadow-sm">
                                        {copy.tagline}
                                    </span>
                                    <a
                                        href="#promotions"
                                        className="rounded-md px-3 py-2 transition hover:bg-slate-50"
                                    >
                                        {copy.navDeals}
                                    </a>
                                </div>
                            </nav>
                        </div>

                        <div className="flex items-center gap-1 sm:gap-2">
                            <button
                                type="button"
                                className="hidden rounded-full border border-[#0f5ea8] px-4 py-1.5 text-sm font-semibold text-[#0f5ea8] transition hover:bg-[#0f5ea8]/5 md:inline-flex"
                            >
                                {copy.listYourPlace}
                            </button>

                            <div className="hidden items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:flex">
                                <button
                                    type="button"
                                    onClick={() =>
                                        switchLocale(locale === "en" ? "km" : "en")
                                    }
                                    aria-label="Switch language"
                                    className="inline-flex items-center gap-1"
                                >
                                    <span className="inline-block h-4 w-4 rounded-sm bg-gradient-to-b from-red-600 via-white to-blue-700" />
                                    <span className="uppercase">
                                        {locale === "km" ? "ខ្មែរ" : "EN"}
                                    </span>
                                </button>
                            </div>

                            <span className="hidden items-center px-2 py-1.5 text-sm font-semibold text-slate-700 sm:inline-flex">
                                USD
                            </span>

                            <a
                                href="/admin/login"
                                className="rounded-full px-3 py-1.5 text-sm font-semibold text-[#0f5ea8] transition hover:bg-[#0f5ea8]/5"
                            >
                                {copy.signIn}
                            </a>
                            <a
                                href="/admin/login"
                                className="hidden rounded-full border border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 sm:inline-flex"
                            >
                                {copy.navAdmin}
                            </a>
                        </div>
                    </div>
                </header>

                {/* HERO */}
                <section
                    className="relative isolate"
                    style={{
                        background:
                            "linear-gradient(135deg, #0f4f8a 0%, #1d7bd1 35%, #62b6cb 75%, #94d2bd 100%)",
                    }}
                >
                    <div
                        aria-hidden="true"
                        className="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_15%,rgba(255,255,255,0.18),transparent_45%),radial-gradient(circle_at_85%_85%,rgba(15,23,42,0.25),transparent_45%)]"
                    />
                    <div className="mx-auto max-w-7xl px-4 pb-14 pt-12 sm:px-6 sm:pb-20 sm:pt-16 lg:px-8">
                        <div className="text-center">
                            <h1 className="text-3xl font-bold leading-tight tracking-tight text-white drop-shadow-md sm:text-4xl lg:text-5xl">
                                {homeData.hero?.headline || copy.heroHeadline}
                            </h1>
                            <p className="mx-auto mt-3 max-w-2xl text-base text-white/90 sm:text-lg">
                                {homeData.hero?.subheadline ||
                                    copy.heroSubheadline}
                            </p>
                        </div>

                        <form
                            onSubmit={handleSearchSubmit}
                            className="relative mx-auto mt-8 max-w-5xl pb-7"
                        >
                            {/* Tabs strip — separate white card centered above the form */}
                            <div className="mx-auto -mb-2 flex w-full max-w-2xl items-end justify-center overflow-x-auto rounded-t-2xl bg-white px-2 pt-2 shadow-[0_-4px_18px_-12px_rgba(15,23,42,0.25)] [scrollbar-width:none] sm:px-4 [&::-webkit-scrollbar]:hidden">
                                {SEARCH_TABS.map((tab) => {
                                    const isActive = tab.id === activeTab;
                                    const Icon = tab.Icon;
                                    return (
                                        <button
                                            type="button"
                                            key={tab.id}
                                            onClick={() => {
                                                setActiveTab(tab.id);
                                                setGuestsOpen(false);
                                            }}
                                            aria-selected={isActive}
                                            className={`relative inline-flex shrink-0 items-center gap-2 px-3 pb-3 pt-2 text-sm font-semibold transition sm:px-4 sm:text-[15px] ${
                                                isActive
                                                    ? "text-[#1d7bd1]"
                                                    : "text-slate-700 hover:text-slate-900"
                                            }`}
                                        >
                                            <Icon className="h-5 w-5" />
                                            {copy.searchTabs[tab.id]}
                                            <span
                                                aria-hidden="true"
                                                className={`absolute inset-x-2 -bottom-px h-[3px] rounded-full transition ${
                                                    isActive
                                                        ? "bg-[#1d7bd1]"
                                                        : "bg-transparent"
                                                }`}
                                            />
                                        </button>
                                    );
                                })}
                            </div>

                            {/* Form body — light slate card with rounded white inputs */}
                            <div className="rounded-2xl bg-slate-100 px-4 pb-12 pt-5 shadow-2xl shadow-slate-900/20 sm:px-6 sm:pt-6">
                                {/* Sub-toggles per tab */}
                                {activeTab === "hotels" ? (
                                    <div className="mb-4 flex items-center gap-2">
                                        {[
                                            {
                                                id: "overnight",
                                                label: copy.stayTypeOvernight,
                                            },
                                            {
                                                id: "dayuse",
                                                label: copy.stayTypeDayUse,
                                            },
                                        ].map((option) => {
                                            const isActive =
                                                stayType === option.id;
                                            return (
                                                <button
                                                    type="button"
                                                    key={option.id}
                                                    onClick={() =>
                                                        setStayType(option.id)
                                                    }
                                                    className={`rounded-full border px-4 py-1.5 text-sm font-semibold transition ${
                                                        isActive
                                                            ? "border-[#1d7bd1] bg-white text-[#1d7bd1]"
                                                            : "border-slate-300 bg-white text-slate-700 hover:border-slate-400"
                                                    }`}
                                                >
                                                    {option.label}
                                                </button>
                                            );
                                        })}
                                    </div>
                                ) : null}

                                {isTransfer ? (
                                    <div className="mb-4 flex items-center gap-2">
                                        {[
                                            {
                                                id: "from",
                                                label: copy.transferFromAirport,
                                            },
                                            {
                                                id: "to",
                                                label: copy.transferToAirport,
                                            },
                                        ].map((option) => {
                                            const isActive =
                                                transferDirection === option.id;
                                            return (
                                                <button
                                                    type="button"
                                                    key={option.id}
                                                    onClick={() =>
                                                        setTransferDirection(
                                                            option.id,
                                                        )
                                                    }
                                                    className={`rounded-full border px-4 py-1.5 text-sm font-semibold transition ${
                                                        isActive
                                                            ? "border-[#1d7bd1] bg-white text-[#1d7bd1]"
                                                            : "border-slate-300 bg-white text-slate-700 hover:border-slate-400"
                                                    }`}
                                                >
                                                    {option.label}
                                                </button>
                                            );
                                        })}
                                    </div>
                                ) : null}

                                {/* Main fields */}
                                {isTransfer ? (
                                    <div className="space-y-3">
                                        <div className="relative grid items-stretch gap-3 sm:grid-cols-2">
                                            <label className="flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-left transition focus-within:ring-2 focus-within:ring-[#1d7bd1]/30">
                                                <IconPlane className="h-5 w-5 shrink-0 text-slate-500" />
                                                <input
                                                    type="text"
                                                    value={pickupAirport}
                                                    onChange={(event) =>
                                                        setPickupAirport(
                                                            event.target.value,
                                                        )
                                                    }
                                                    placeholder={
                                                        copy.pickupAirportPlaceholder
                                                    }
                                                    className="w-full bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:font-normal placeholder:text-slate-400"
                                                />
                                            </label>
                                            <label className="flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-left transition focus-within:ring-2 focus-within:ring-[#1d7bd1]/30">
                                                <IconPin className="h-5 w-5 shrink-0 text-slate-500" />
                                                <input
                                                    type="text"
                                                    value={destinationLocation}
                                                    onChange={(event) =>
                                                        setDestinationLocation(
                                                            event.target.value,
                                                        )
                                                    }
                                                    placeholder={
                                                        copy.destinationLocationPlaceholder
                                                    }
                                                    className="w-full bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:font-normal placeholder:text-slate-400"
                                                />
                                            </label>
                                            <button
                                                type="button"
                                                onClick={handleSwapTransferEnds}
                                                aria-label="Swap pickup and destination"
                                                className="absolute left-1/2 top-1/2 hidden -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-md border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:border-[#1d7bd1] hover:text-[#1d7bd1] sm:inline-flex"
                                            >
                                                <IconSwap className="h-4 w-4" />
                                            </button>
                                        </div>
                                        <div className="grid gap-3 sm:grid-cols-2">
                                            <div className="flex items-stretch gap-2 rounded-xl bg-white px-4 py-3">
                                                <div className="flex flex-1 items-center gap-3">
                                                    <IconCalendarIn className="h-5 w-5 shrink-0 text-slate-500" />
                                                    <label className="flex w-full flex-col">
                                                        <span className="text-[11px] font-medium uppercase tracking-wide text-slate-500">
                                                            {copy.pickupDateLabel}
                                                        </span>
                                                        <input
                                                            type="date"
                                                            value={checkIn}
                                                            min={todayPlus(0)}
                                                            onChange={(event) =>
                                                                setCheckIn(
                                                                    event.target
                                                                        .value,
                                                                )
                                                            }
                                                            className="w-full bg-transparent text-sm font-semibold text-slate-900 outline-none"
                                                        />
                                                    </label>
                                                </div>
                                                <div className="flex items-center gap-2 border-l border-slate-200 pl-3">
                                                    <IconClock className="h-5 w-5 text-slate-500" />
                                                    <input
                                                        type="time"
                                                        value={pickupTime}
                                                        onChange={(event) =>
                                                            setPickupTime(
                                                                event.target
                                                                    .value,
                                                            )
                                                        }
                                                        className="bg-transparent text-sm font-semibold text-slate-900 outline-none"
                                                    />
                                                </div>
                                            </div>
                                            <div className="relative">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setGuestsOpen(
                                                            (prev) => !prev,
                                                        )
                                                    }
                                                    aria-expanded={guestsOpen}
                                                    className="flex h-full w-full items-center gap-3 rounded-xl bg-white px-4 py-3 text-left transition hover:ring-1 hover:ring-slate-300"
                                                >
                                                    <IconUsers className="h-5 w-5 shrink-0 text-slate-500" />
                                                    <span className="flex-1 truncate text-sm font-semibold text-slate-900">
                                                        {copy.passengersLabel(
                                                            guests.passengers ||
                                                                1,
                                                        )}
                                                    </span>
                                                    <IconChevronDown className="h-4 w-4 shrink-0 text-slate-500" />
                                                </button>
                                                {guestsOpen ? (
                                                    <GuestsPopover
                                                        values={guests}
                                                        onChange={setGuests}
                                                        copy={copy}
                                                        onClose={() =>
                                                            setGuestsOpen(false)
                                                        }
                                                        rows={[
                                                            {
                                                                key: "passengers",
                                                                label: copy.passengersOnlyLabel,
                                                                min: 1,
                                                            },
                                                        ]}
                                                    />
                                                ) : null}
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="space-y-3">
                                        <label className="flex items-center gap-3 rounded-xl bg-white px-4 py-3 text-left transition focus-within:ring-2 focus-within:ring-[#1d7bd1]/30">
                                            <IconSearch className="h-5 w-5 shrink-0 text-slate-500" />
                                            <input
                                                type="text"
                                                value={destinationQuery}
                                                onChange={(event) =>
                                                    setDestinationQuery(
                                                        event.target.value,
                                                    )
                                                }
                                                placeholder={
                                                    copy.destinationPlaceholder
                                                }
                                                className="w-full bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:font-normal placeholder:text-slate-400"
                                            />
                                        </label>

                                        <div className="grid gap-3 sm:grid-cols-[1fr_1fr_1fr]">
                                            <DateField
                                                Icon={IconCalendarIn}
                                                value={checkIn}
                                                min={todayPlus(0)}
                                                onChange={(value) => {
                                                    setCheckIn(value);
                                                    if (value >= checkOut) {
                                                        const next = new Date(
                                                            value,
                                                        );
                                                        next.setDate(
                                                            next.getDate() + 1,
                                                        );
                                                        setCheckOut(
                                                            next
                                                                .toISOString()
                                                                .slice(0, 10),
                                                        );
                                                    }
                                                }}
                                                main={checkInParts.main}
                                                sub={checkInParts.sub}
                                            />
                                            <DateField
                                                Icon={IconCalendarOut}
                                                value={checkOut}
                                                min={checkIn}
                                                onChange={setCheckOut}
                                                main={checkOutParts.main}
                                                sub={checkOutParts.sub}
                                            />
                                            <div className="relative">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setGuestsOpen(
                                                            (prev) => !prev,
                                                        )
                                                    }
                                                    aria-expanded={guestsOpen}
                                                    className="flex h-full w-full items-center gap-3 rounded-xl bg-white px-4 py-3 text-left transition hover:ring-1 hover:ring-slate-300"
                                                >
                                                    <IconUsers className="h-5 w-5 shrink-0 text-slate-500" />
                                                    <span className="flex-1 truncate">
                                                        <span className="block text-sm font-semibold text-slate-900">
                                                            {copy.guestsAdultsOnly(
                                                                guests.adults,
                                                            )}
                                                        </span>
                                                        {hasRooms ? (
                                                            <span className="block text-xs font-medium text-slate-500">
                                                                {copy.guestsRoomsLine(
                                                                    guests.rooms,
                                                                )}
                                                            </span>
                                                        ) : null}
                                                    </span>
                                                    <IconChevronDown className="h-4 w-4 shrink-0 text-slate-500" />
                                                </button>
                                                {guestsOpen ? (
                                                    <GuestsPopover
                                                        values={guests}
                                                        onChange={setGuests}
                                                        copy={copy}
                                                        onClose={() =>
                                                            setGuestsOpen(false)
                                                        }
                                                        rows={
                                                            hasRooms
                                                                ? [
                                                                      {
                                                                          key: "adults",
                                                                          label: copy.adultsLabel,
                                                                          min: 1,
                                                                      },
                                                                      {
                                                                          key: "children",
                                                                          label: copy.childrenLabel,
                                                                          min: 0,
                                                                      },
                                                                      {
                                                                          key: "rooms",
                                                                          label: copy.roomsLabel,
                                                                          min: 1,
                                                                      },
                                                                  ]
                                                                : [
                                                                      {
                                                                          key: "adults",
                                                                          label: copy.adultsLabel,
                                                                          min: 1,
                                                                      },
                                                                      {
                                                                          key: "children",
                                                                          label: copy.childrenLabel,
                                                                          min: 0,
                                                                      },
                                                                  ]
                                                        }
                                                    />
                                                ) : null}
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Overhanging SEARCH button */}
                            <div className="pointer-events-none absolute inset-x-0 -bottom-1 flex justify-center">
                                <button
                                    type="submit"
                                    className="pointer-events-auto inline-flex h-14 min-w-[260px] items-center justify-center rounded-full bg-[#1d7bd1] px-12 text-base font-bold uppercase tracking-wider text-white shadow-xl shadow-[#1d7bd1]/40 transition hover:bg-[#1668b3] focus:outline-none focus-visible:ring-4 focus-visible:ring-white/50"
                                >
                                    {copy.searchCta}
                                </button>
                            </div>
                        </form>

                        {toast ? (
                            <div className="mx-auto mt-4 max-w-5xl rounded-xl border border-white/30 bg-white/95 px-4 py-3 text-sm font-medium text-slate-800 shadow-lg">
                                {toast}
                            </div>
                        ) : null}
                    </div>
                </section>

                {/* TOP DESTINATIONS */}
                <section
                    id="destinations"
                    className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
                >
                    <div className="flex items-end justify-between gap-3 pb-5">
                        <h2 className="text-xl font-bold text-slate-900 sm:text-2xl">
                            {copy.topDestinations}
                        </h2>
                        <a
                            href="#"
                            className="hidden text-sm font-semibold text-[#0f5ea8] hover:underline sm:inline"
                        >
                            {copy.viewAll} →
                        </a>
                    </div>
                    <Carousel ariaLabel={copy.topDestinations}>
                        {safeDestinations.map((destination, index) => (
                            <DestinationCard
                                key={destination.id}
                                destination={destination}
                                gradient={
                                    destinationGradients[
                                        index % destinationGradients.length
                                    ]
                                }
                                copy={copy}
                            />
                        ))}
                    </Carousel>
                </section>

                {/* STAYS WE THINK YOU'LL LOVE */}
                <section
                    id="stays"
                    className="bg-slate-50"
                >
                    <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                        <div className="flex items-end justify-between gap-3 pb-5">
                            <h2 className="text-xl font-bold text-slate-900 sm:text-2xl">
                                {copy.featuredStays}
                            </h2>
                            <a
                                href="#"
                                className="hidden text-sm font-semibold text-[#0f5ea8] hover:underline sm:inline"
                            >
                                {copy.viewAll} →
                            </a>
                        </div>
                        <Carousel ariaLabel={copy.featuredStays}>
                            {safeProperties.map((property) => (
                                <PropertyCard
                                    key={property.id}
                                    property={property}
                                    copy={copy}
                                    locale={locale}
                                />
                            ))}
                        </Carousel>
                    </div>
                </section>

                {/* PROMOTIONS */}
                <section
                    id="promotions"
                    className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
                >
                    <div className="flex items-end justify-between gap-3 pb-5">
                        <h2 className="text-xl font-bold text-slate-900 sm:text-2xl">
                            {copy.promotions}
                        </h2>
                        <a
                            href="#"
                            className="hidden text-sm font-semibold text-[#0f5ea8] hover:underline sm:inline"
                        >
                            {copy.viewAll} →
                        </a>
                    </div>
                    <Carousel ariaLabel={copy.promotions}>
                        {safePromotions.map((promo, index) => (
                            <PromotionCard
                                key={promo.id}
                                promo={promo}
                                gradient={
                                    promotionGradients[
                                        index % promotionGradients.length
                                    ]
                                }
                                copy={copy}
                            />
                        ))}
                    </Carousel>
                </section>

                {/* PROPERTY TYPES */}
                <section id="types" className="bg-slate-50">
                    <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                        <div className="pb-5">
                            <h2 className="text-xl font-bold text-slate-900 sm:text-2xl">
                                {copy.propertyTypes}
                            </h2>
                        </div>
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            {safePropertyTypes.map((type) => (
                                <a
                                    key={type.id}
                                    href="#"
                                    className="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-800 transition hover:border-[#0f5ea8] hover:bg-[#0f5ea8]/5 hover:shadow"
                                >
                                    <span>{type.name}</span>
                                    <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                        {formatCompact(type.propertyCount)}
                                    </span>
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* REVIEWS */}
                <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div className="pb-5">
                        <h2 className="text-xl font-bold text-slate-900 sm:text-2xl">
                            {copy.reviews}
                        </h2>
                    </div>
                    <div className="grid gap-4 md:grid-cols-3">
                        {safeReviews.map((review) => (
                            <article
                                key={review.id}
                                className="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                            >
                                <div className="flex items-center gap-3">
                                    <span className="grid h-10 w-10 place-items-center rounded-full bg-[#0f5ea8] text-sm font-bold text-white">
                                        {review.guestName?.slice(-2) || "GU"}
                                    </span>
                                    <div>
                                        <p className="text-sm font-semibold text-slate-900">
                                            {review.guestName}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            {[review.city, review.date]
                                                .filter(Boolean)
                                                .join(" · ")}
                                        </p>
                                    </div>
                                    <span className="ml-auto inline-flex items-center rounded-md bg-[#0f5ea8] px-2 py-1 text-xs font-bold text-white">
                                        {Number(review.rating).toFixed(1)}
                                    </span>
                                </div>
                                <div>
                                    <h3 className="text-sm font-semibold text-slate-900">
                                        {review.title}
                                    </h3>
                                    <p className="mt-1 text-sm text-slate-600">
                                        {review.comment}
                                    </p>
                                </div>
                                {review.propertyName ? (
                                    <p className="mt-auto text-xs font-medium text-[#0f5ea8]">
                                        {review.propertyName}
                                    </p>
                                ) : null}
                            </article>
                        ))}
                    </div>
                </section>

                {/* APP DOWNLOAD CARD */}
                <section className="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                    <div className="flex flex-col gap-6 rounded-3xl bg-gradient-to-r from-[#0f4f8a] via-[#1d7bd1] to-[#62b6cb] p-6 text-white shadow-xl sm:flex-row sm:items-center sm:justify-between sm:p-8">
                        <div className="max-w-xl">
                            <h2 className="text-2xl font-bold sm:text-3xl">
                                {copy.appHeading}
                            </h2>
                            <p className="mt-2 text-sm text-white/85 sm:text-base">
                                {copy.appBody}
                            </p>
                        </div>
                        <div className="grid h-32 w-32 shrink-0 place-items-center rounded-2xl bg-white p-3 text-slate-900 shadow-lg">
                            <svg
                                viewBox="0 0 64 64"
                                role="img"
                                aria-label="QR code placeholder"
                                className="h-full w-full"
                            >
                                <rect width="64" height="64" fill="white" />
                                <g fill="#0f172a">
                                    <rect x="2" y="2" width="14" height="14" />
                                    <rect x="5" y="5" width="8" height="8" fill="white" />
                                    <rect x="7" y="7" width="4" height="4" />
                                    <rect x="48" y="2" width="14" height="14" />
                                    <rect x="51" y="5" width="8" height="8" fill="white" />
                                    <rect x="53" y="7" width="4" height="4" />
                                    <rect x="2" y="48" width="14" height="14" />
                                    <rect x="5" y="51" width="8" height="8" fill="white" />
                                    <rect x="7" y="53" width="4" height="4" />
                                    <rect x="20" y="4" width="3" height="3" />
                                    <rect x="26" y="4" width="3" height="3" />
                                    <rect x="32" y="4" width="3" height="3" />
                                    <rect x="38" y="4" width="3" height="3" />
                                    <rect x="20" y="10" width="3" height="3" />
                                    <rect x="32" y="10" width="3" height="3" />
                                    <rect x="44" y="20" width="3" height="3" />
                                    <rect x="50" y="20" width="3" height="3" />
                                    <rect x="56" y="20" width="3" height="3" />
                                    <rect x="20" y="20" width="3" height="3" />
                                    <rect x="26" y="20" width="3" height="3" />
                                    <rect x="32" y="20" width="3" height="3" />
                                    <rect x="20" y="26" width="3" height="3" />
                                    <rect x="32" y="26" width="3" height="3" />
                                    <rect x="38" y="26" width="3" height="3" />
                                    <rect x="44" y="26" width="3" height="3" />
                                    <rect x="50" y="26" width="3" height="3" />
                                    <rect x="20" y="32" width="3" height="3" />
                                    <rect x="26" y="32" width="3" height="3" />
                                    <rect x="38" y="32" width="3" height="3" />
                                    <rect x="56" y="32" width="3" height="3" />
                                    <rect x="20" y="38" width="3" height="3" />
                                    <rect x="32" y="38" width="3" height="3" />
                                    <rect x="44" y="38" width="3" height="3" />
                                    <rect x="50" y="38" width="3" height="3" />
                                    <rect x="20" y="44" width="3" height="3" />
                                    <rect x="26" y="44" width="3" height="3" />
                                    <rect x="32" y="44" width="3" height="3" />
                                    <rect x="38" y="44" width="3" height="3" />
                                    <rect x="44" y="44" width="3" height="3" />
                                    <rect x="50" y="44" width="3" height="3" />
                                    <rect x="56" y="44" width="3" height="3" />
                                    <rect x="26" y="50" width="3" height="3" />
                                    <rect x="38" y="50" width="3" height="3" />
                                    <rect x="44" y="50" width="3" height="3" />
                                    <rect x="56" y="50" width="3" height="3" />
                                    <rect x="26" y="56" width="3" height="3" />
                                    <rect x="32" y="56" width="3" height="3" />
                                    <rect x="38" y="56" width="3" height="3" />
                                    <rect x="44" y="56" width="3" height="3" />
                                    <rect x="56" y="56" width="3" height="3" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </section>

                {/* FOOTER */}
                <footer className="bg-slate-900 text-slate-300">
                    <div className="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-5 lg:px-8">
                        <div className="lg:col-span-1">
                            <div className="flex items-center gap-2">
                                <span className="grid h-9 w-9 place-items-center rounded-full bg-white text-sm font-bold text-[#0f5ea8]">
                                    BE
                                </span>
                                <span className="text-lg font-semibold text-white">
                                    {appName}
                                </span>
                            </div>
                            <p className="mt-3 text-sm text-slate-400">
                                {copy.heroSubheadline}
                            </p>
                        </div>

                        {[
                            {
                                title: copy.footerHelp,
                                links: [
                                    ["Help center", "#"],
                                    ["FAQs", "#"],
                                    ["Privacy policy", "#"],
                                    ["Terms of use", "#"],
                                ],
                            },
                            {
                                title: copy.footerCompany,
                                links: [
                                    ["About us", "#"],
                                    ["Careers", "#"],
                                    ["Press", "#"],
                                    ["Blog", "#"],
                                ],
                            },
                            {
                                title: copy.footerDestinations,
                                links: safeDestinations
                                    .slice(0, 6)
                                    .map((d) => [d.name, "#"]),
                            },
                            {
                                title: copy.footerPartners,
                                links: [
                                    ["List your place", "#"],
                                    ["Partner hub", "#"],
                                    ["Advertise", "#"],
                                    ["Affiliates", "#"],
                                ],
                            },
                        ].map((column) => (
                            <div key={column.title}>
                                <h3 className="text-sm font-semibold uppercase tracking-wider text-white">
                                    {column.title}
                                </h3>
                                <ul className="mt-4 space-y-2 text-sm">
                                    {column.links.map(([label, href]) => (
                                        <li key={label}>
                                            <a
                                                href={href}
                                                className="text-slate-400 transition hover:text-white"
                                            >
                                                {label}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                    <div className="border-t border-slate-800">
                        <div className="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                            <span>{copy.footerCopyright}</span>
                            <span>
                                {formatCompact(homeData.stats?.approvedProperties || 0)}{" "}
                                approved stays ·{" "}
                                {formatCompact(homeData.stats?.activeDestinations || 0)}{" "}
                                destinations ·{" "}
                                {formatCompact(homeData.stats?.approvedReviews || 0)}{" "}
                                verified reviews
                            </span>
                        </div>
                    </div>
                </footer>
            </div>

            <DateDisplay locale={locale} checkIn={checkIn} checkOut={checkOut} />
        </>
    );
}

function DateDisplay({ locale, checkIn, checkOut }) {
    const inParts = splitDateDisplay(formatDateDisplay(checkIn, locale));
    const outParts = splitDateDisplay(formatDateDisplay(checkOut, locale));
    return (
        <div className="sr-only">
            <span>
                {inParts.main} {inParts.sub} — {outParts.main} {outParts.sub}
            </span>
        </div>
    );
}
