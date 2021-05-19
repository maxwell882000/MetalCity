from telegram import Update
from telegram.ext import ConversationHandler, CommandHandler, MessageHandler, Filters
from core.resources import strings, keyboards
from core.services import users
from .utils import Navigation
from core.resources import  images
LANGUAGES = 1


def referral_start(update: Update, context):
    user = users.user_exists(update.message.from_user.id)
    if user:
        if user.get('is_blocked'):
            blocked_message = strings.get_string('blocked', user.get('language'))
            update.message.reply_text(blocked_message)
            return ConversationHandler.END
        welcome_message = strings.get_string('start.welcome', user.get('language')).format(username=_get_user_name(update.effective_user))
        remove_keyboard = keyboards.get_keyboard('remove')
        update.message.reply_text(welcome_message, reply_markup=remove_keyboard)
        Navigation.to_account(update, context, new_message=True)
        return ConversationHandler.END
    if context.args:
        context.user_data['referral_from_id'] = context.args[0]
    languages_message = strings.get_string('start.languages')
    keyboard = keyboards.get_keyboard('start.languages')

    chat_id = update.effective_message.chat_id
    image = images.get_start_image()

    context.bot.send_photo(chat_id=chat_id, photo=image, caption = 'Добро пожаловать в бот!')
    update.message.reply_text(languages_message, reply_markup=keyboard)

    return LANGUAGES


def languages(update: Update, context):
    def error():
        languages_message = strings.get_string('start.languages')
        keyboard = keyboards.get_keyboard('start.languages')
        update.message.reply_text(languages_message, reply_markup=keyboard)

    text = update.message.text
    if strings.get_string('languages.ru') in text:
        language = 'ru'
    elif strings.get_string('languages.uz') in text:
        language = 'uz'
    else:
        error()
        return LANGUAGES
    user = update.message.from_user
    user_name = _get_user_name(user)
    users.create_user(user.id, user_name, user.username, language,
                      referral_from_id=context.user_data.get('referral_from_id', None))
    help_message = strings.get_string('start.help', language)
    remove_keyboard = keyboards.get_keyboard('remove')
    update.message.reply_text(help_message, reply_markup=remove_keyboard)
    Navigation.to_account(update, context, new_message=True)
    return ConversationHandler.END


def _get_user_name(user):
    user_name = user.first_name
    if user.last_name:
        user_name += (" " + user.last_name)
    return user_name


def cancel():
    pass

def image(update: Update, context):
    chat_id = update.effective_message.chat_id
    image = open('core/resources/images/citymetl.jpg', 'r')
    context.bot.send_photo(chat_id=chat_id, photo=image)
    languages(update, context)


conversation_handler = ConversationHandler(
    entry_points=[CommandHandler('start', referral_start, pass_args=True)],
    states={
        LANGUAGES: [MessageHandler(Filters.text, languages)]
    },
    fallbacks=[MessageHandler(Filters.text, '')]
)
