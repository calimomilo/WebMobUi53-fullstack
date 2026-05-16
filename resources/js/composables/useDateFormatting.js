const dateLocale = 'fr-CH';
const dateOptions = {
weekday: "long",
day: "numeric",
month: "long",
year: "numeric",
hour: "numeric",
minute: "numeric",
};

export function useDateFormatting() {
    function toFormattedDate(date) {
        return date? new Date(date).toLocaleString(dateLocale, dateOptions) : '-';
    }

    return { toFormattedDate }
}