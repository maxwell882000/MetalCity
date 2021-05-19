from telegram import ParseMode
from telegram.ext import ConversationHandler
from core.resources import strings, keyboards, images
from core.bot.utils import Navigation, Notifications
from core.services import categories, vacations, settings, users
from core.bot import payments
from datetime import datetime
import copy

TARIFFS, PROVIDER, PRE_CHECKOUT, HISTORY, TITLE, PRICE, UNIT, CONTACTS, REGION, CATEGORIES, CONFIRM = range(11)

def to_parent_categories(query, context):
    parent_categories = categories.get_parent_categories()
    parent_categories = sorted(parent_categories, key=lambda i: i['position'])
    language = context.user_data['user'].get('language')
    message = strings.get_string('vacations.create.categories', language)
    keyboard = keyboards.get_parent_categories_keyboard(parent_categories, language)
    query.answer()
    try:
        context.bot.delete_message(chat_id=query.from_user.id, message_id=query.message.message_id)
    except:
        pass
    message = context.bot.send_message(chat_id=query.from_user.id, text=message, reply_markup=keyboard, parse_mode=ParseMode.HTML)
    context.user_data['categories_message_id'] = message.message_id
    return CATEGORIES

def from_location_to_title(update, context):
    language = context.user_data['user'].get('language')
    try:
        context.bot.delete_message(chat_id=update.callback_query.message.chat_id,
                               message_id=context.user_data['location_message_id'])
    except:
        pass
    message = strings.get_string('vacations.create.title', language)
    keyboard = keyboards.get_keyboard('go_back.inline', language)
    message = context.bot.send_message(chat_id=update.callback_query.message.chat_id,
                             text=message,
                             reply_markup=keyboard,
                             parse_mode=ParseMode.HTML)
    context.user_data['title_message_id'] = message.message_id
    return TITLE

def from_categories_to_menu(update, context):
    del context.user_data['vacation']
    Navigation.to_account(update, context)
    del context.user_data['has_action']
    return ConversationHandler.END

def create(update, context):
    context.user_data['user'] = users.user_exists(update.callback_query.from_user.id)
    if context.user_data['user'].get('is_blocked'):
        blocked_message = strings.get_string('blocked', context.user_data['user'].get('language'))
        update.callback_query.answer(text=blocked_message, show_alert=True)
        return ConversationHandler.END
    context.user_data['has_action'] = True
    query = update.callback_query
    context.user_data['vacation'] = {}
    context.user_data['vacation']['user_id'] = query.from_user.id
    context.user_data['vacation']['name'] = query.from_user.first_name
    language = context.user_data['user'].get('language')
    query.answer(text=strings.get_string('vacations.menu_has_gone', language), show_alert=True)
    return to_parent_categories(query, context)

def vacation_title(update, context):
    language = context.user_data['user'].get('language')
    if update.callback_query and update.callback_query.data == 'back':
        return to_parent_categories(update.callback_query, context)
    context.user_data['vacation']['title'] = update.message.text
    chat_id = update.message.chat.id if update.message else update.callback_query.from_user.id
    try:
        context.bot.delete_message(chat_id=chat_id, message_id=context.user_data['title_message_id'])
    except:
        pass
    language = context.user_data['user'].get('language')
    message = strings.get_string('vacations.create.location', language)
    keyboard = keyboards.get_keyboard('location.regions', language)
    message = update.message.reply_text(text=message, reply_markup=keyboard, parse_mode=ParseMode.HTML)
    context.user_data['location_message_id'] = message.message_id
    return REGION

def vacation_price(update, context):
    chat_id = update.message.chat.id if update.message else update.callback_query.from_user.id
    try:
        context.bot.delete_message(chat_id=chat_id, message_id=context.user_data['price_message_id'])
    except:
        pass
    language = context.user_data['user'].get('language')
    if update.callback_query and update.callback_query.data == 'back':
        message = strings.get_string('vacations.create.unit', language)
        keyboard = keyboards.get_units_keyboard(language)
        message = context.bot.send_message(chat_id=chat_id, text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
        context.user_data['units_message_id'] = message.message_id
        return UNIT
    context.user_data['vacation']['price'] = update.message.text
    message = strings.get_string('vacations.create.contacts', language)
    keyboard = keyboards.get_keyboard('go_back.inline', language)
    message = context.bot.send_message(chat_id=chat_id, text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    context.user_data['contacts_message_id'] = message.message_id
    return CONTACTS

def vacation_unit(update, context):
    query = update.callback_query
    chat_id = update.message.chat.id if update.message else update.callback_query.from_user.id
    language = context.user_data['user'].get('language')
    if query and query.data == 'back':
        message = strings.get_string('vacations.create.location', language)
        keyboard = keyboards.get_keyboard('location.regions', language)
        message = query.edit_message_text(text=message, reply_markup=keyboard, parse_mode=ParseMode.HTML)
        context.user_data['location_message_id'] = query.message.message_id
        return REGION
    context.user_data['vacation']['unit'] = int(update.callback_query.data)
    message = strings.get_string('vacations.create.price', language)
    keyboard = keyboards.get_keyboard('go_back.inline', language)
    query.edit_message_text(text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    context.user_data['price_message_id'] = query.message.message_id
    return PRICE

# TODO: maybe change strings.from_vacation
def recursive_add_category(category, language):
    message = category[language + '_title']
    parent_category = category.get('parent_category')
    if parent_category:
        return message + ' - ' + recursive_add_category(categories.get_category(parent_category['id']), language)
    return message

def vacation_contacts(update, context):
    chat_id = update.message.chat.id if update.message else update.callback_query.from_user.id
    try:
        context.bot.delete_message(chat_id=chat_id, message_id=context.user_data['contacts_message_id'])
    except:
        pass
    language = context.user_data['user'].get('language')
    if update.callback_query and update.callback_query.data == 'back':
        message = strings.get_string('vacations.create.price', language)
        keyboard = keyboards.get_keyboard('go_back.inline', language)
        message = context.bot.send_message(chat_id=chat_id, text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
        context.user_data['price_message_id'] = message.message_id
        return PRICE
    phone_number = update.message.text
    phone_number = '+998' + phone_number
    context.user_data['vacation']['contacts'] = phone_number
    vacation = copy.deepcopy(context.user_data['vacation'])
    vacation['created_at'] = datetime.now().strftime('%Y-%m-%dT%H:%M:%S.%fZ')
    vacation['location'] = context.user_data['vacation']['location']['code']
    message = strings.get_string('vacations.create.confirm', language) + '\n\n' + strings.from_vacation(vacation, language)
    keyboard = keyboards.get_vacation_confirm_keyboard(language)
    update.message.reply_text(text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    return CONFIRM

def vacation_region(update, context):
    language = context.user_data['user'].get('language')
    query = update.callback_query
    region = query.data.split(':')[1]
    context.user_data['vacation']['location'] = {}
    context.user_data['vacation']['location']['full_name'] = strings.get_string("location.regions." + region, language)
    context.user_data['vacation']['location']['code'] = region
    query.answer(text=context.user_data['vacation']['location']['full_name'])
    message = strings.get_string('vacations.create.unit', language)
    keyboard = keyboards.get_units_keyboard(language)
    query.edit_message_text(message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
    context.user_data['units_message_id'] = query.message.message_id
    return UNIT

def vacation_confirm(update, context):
    user = context.user_data['user']
    language = user.get('language')
    query = update.callback_query
    context.user_data['user'] = users.user_exists(query.from_user.id)
    if update.callback_query and update.callback_query.data == 'back':
        message = strings.get_string('vacations.create.contacts', language)
        keyboard = keyboards.get_keyboard('go_back.inline', language)
        query.edit_message_text(text=message, parse_mode=ParseMode.HTML, reply_markup=keyboard)
        context.user_data['contacts_message_id'] = query.message.message_id
        return CONTACTS
    if context.user_data['user'].get('is_blocked'):
        blocked_message = strings.get_string('blocked', context.user_data['user'].get('language'))
        update.callback_query.answer(text=blocked_message, show_alert=True)
        return ConversationHandler.END
    if user.get(user.get('user_role') + '_tariff') or user.get('free_actions_count') > 0:
        payment_settings = settings.get_settings()
        item_cost = payment_settings.get(user.get(user.get('user_role') + '_tariff'))
        user_balance = user.get('balance_' + user.get('user_role'))
        if user_balance is None:
            user_balance = 0
        if user_balance >= int(item_cost) or user.get('free_actions_count') > 0:
            result = vacations.create_vacation(context.user_data['vacation'])
            vacation = result.get('vacation')
            context.user_data['user'] = vacation.get('user')
            success_message = strings.get_string('vacations.create.success', language)
            help_message = strings.get_string('vacations.create.success.help', language)
            try:
                context.bot.delete_message(chat_id=query.from_user.id, message_id=query.message.message_id)
            except:
                pass
            context.bot.send_message(chat_id=query.from_user.id, text=success_message)

            notification_group = payment_settings.get('notification_group')
            notification_text = strings.get_notification_string(vacation, language)
            if notification_group:
                try:
                    context.bot.send_message(chat_id=notification_group, text=notification_text, parse_mode="HTML")
                except Exception as e:
                    pass

            Navigation.to_account(update, context, new_message=True)
            del context.user_data['vacation']
            del context.user_data['has_action']
            Notifications.notify_users_new_item(context.bot, result.get('notifyUsers'), 'vacations.notify.new')
            if result.get('notifyUsers'):
                Notifications.notify_users_new_item(context.bot, [user], 'resumes.notify.new')
            return ConversationHandler.END
    empty_balance = strings.get_string('empty_balance', language)
    query.answer(text=empty_balance, show_alert=True)
    return payments.start(update, context)

def vacation_categories(update, context):
    user = context.user_data['user']
    language = user.get('language')
    query = update.callback_query
    category_id = query.data.split(':')[1]
    if category_id == 'back':
        current_category = context.user_data['current_category']
        if current_category.get('parent_id'):
            siblings_category = categories.get_siblings(current_category.get('id'))
            siblings_category = sorted(siblings_category, key=lambda i: i['position'])
            message = strings.get_category_description(current_category, language)
            keyboard = keyboards.get_categories_keyboard(siblings_category, language,
                                                         context.user_data['vacation']['categories'])
            query.answer()
            query.edit_message_text(text=message, reply_markup=keyboard)
            context.user_data['current_category'] = categories.get_category(current_category.get('parent_id'))
            return CATEGORIES
        else:
            return to_parent_categories(query, context)
    if category_id == 'save':
        message = strings.get_string('vacations.create.title', language)
        keyboard = keyboards.get_keyboard('go_back.inline', language)
        try:
            context.bot.delete_message(chat_id=query.message.chat.id, message_id=query.message.message_id)
        except:
            pass
        message = context.bot.send_message(chat_id=update.callback_query.message.chat_id,
                             text=message,
                             reply_markup=keyboard,
                             parse_mode=ParseMode.HTML)
        context.user_data['title_message_id'] = message.message_id
        return TITLE

    category = categories.get_category(category_id)
    children_categories = category.get('categories')
    if 'categories' not in context.user_data['vacation']:
        context.user_data['vacation']['categories'] = []
    if children_categories:
        children_categories = sorted(children_categories, key=lambda i: i['position'])
        keyboard = keyboards.get_categories_keyboard(children_categories, language,
                                                     context.user_data['vacation']['categories'])
        message = strings.get_category_description(category, language)
        query.edit_message_text(message, reply_markup=keyboard)
        context.user_data['current_category'] = category
        return CATEGORIES
    else:
        if any(d['id'] == category['id'] for d in context.user_data['vacation']['categories']):
            added = False
            context.user_data['vacation']['categories'][:] = [c for c in context.user_data['vacation']['categories'] if
                                                            c.get('id') != category.get('id')]
        else:
            if len(context.user_data['vacation']['categories']) == 10:
                limit_message = strings.get_string('categories.limit', language)
                query.answer(text=limit_message, show_alert=True)
                return CATEGORIES
            added = True
            context.user_data['vacation']['categories'].append(category)
        category_siblings = categories.get_siblings(category_id)
        category_siblings = sorted(category_siblings, key=lambda i: i['position'])
        keyboard = keyboards.get_categories_keyboard(category_siblings, language,
                                                     context.user_data['vacation']['categories'])
        message = strings.from_categories(category, context.user_data['vacation']['categories'], added, language)
        answer_message = strings.from_categories_message(category, context.user_data['vacation']['categories'], added,
                                                         language)
        query.answer(text=answer_message)
        query.edit_message_text(message, reply_markup=keyboard)
        return CATEGORIES
